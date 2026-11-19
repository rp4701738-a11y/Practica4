
<?php
require_once "permitidos.php";
protegerRuta();

// Identificador del usuario conectado.
// Comprueba que permitidos.php utiliza esta clave de sesión.
$idUsuario = $_SESSION["IdUsuario"] ?? null;

function OpenConnection()
{
    $conn = new mysqli("localhost", "wwwdata", "abc", "pibd");

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");
    return $conn;
}

$conn = OpenConnection();

$mensaje = "";
$exito = false;

// ==================================================
// PROCESAR FORMULARIO EN ESTA MISMA PÁGINA
// ==================================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!$idUsuario || !is_numeric($idUsuario)) {
        $mensaje = "No se ha podido identificar al usuario conectado.";
    } else {

        $tipoAnuncio = (int)($_POST["tipoAnuncio"] ?? 0);
        $tipoVivienda = (int)($_POST["tipoVivienda"] ?? 0);
        $titulo = trim($_POST["titulo"] ?? "");
        $precio = $_POST["precio"] ?? "";
        $descripcion = trim($_POST["descripcion"] ?? "");
        $caracteristicas = trim($_POST["caracteristicas"] ?? "");
        $ciudad = trim($_POST["ciudad"] ?? "");
        $pais = (int)($_POST["pais"] ?? 0);

        // Textos alternativos obligatorios según la base de datos
        $alternativo = trim($_POST["alternativo"] ?? "");
        $alternativoFotos = trim($_POST["alternativoFotos"] ?? "");

        if (
            $tipoAnuncio <= 0 ||
            $tipoVivienda <= 0 ||
            $pais <= 0 ||
            $titulo === "" ||
            $alternativo === "" ||
            mb_strlen($alternativo) < 10
        ) {
            $mensaje = "Comprueba los campos obligatorios y el texto alternativo.";

        } elseif (
            $precio === "" ||
            !is_numeric($precio) ||
            !is_finite((float)$precio) ||
            (float)$precio < 0 ||
            (float)$precio > 9999999999.99
        ) {
            $mensaje = "Introduce un precio válido.";

        } elseif (
            !empty($_FILES["miniaturas"]["name"][0]) &&
            mb_strlen($alternativoFotos) < 10
        ) {
            $mensaje = "El texto alternativo de las fotografías debe tener al menos 10 caracteres.";

        } else {

            $archivosGuardados = [];
            $transaccionIniciada = false;

            try {

                // Comprobar que existen los registros relacionados
                $comprobar = $conn->prepare("
                    SELECT
                        (SELECT COUNT(*) FROM TiposAnuncios WHERE IdTAnuncio = ?),
                        (SELECT COUNT(*) FROM TiposViviendas WHERE IdTVivienda = ?),
                        (SELECT COUNT(*) FROM Paises WHERE IdPais = ?),
                        (SELECT COUNT(*) FROM Usuarios WHERE IdUsuario = ?)
                ");

                $comprobar->bind_param(
                    "iiii",
                    $tipoAnuncio,
                    $tipoVivienda,
                    $pais,
                    $idUsuario
                );

                $comprobar->execute();
                $comprobar->bind_result($existeTipo, $existeVivienda, $existePais, $existeUsuario);
                $comprobar->fetch();
                $comprobar->close();

                if (!$existeTipo || !$existeVivienda || !$existePais || !$existeUsuario) {
                    throw new Exception("Alguno de los datos seleccionados no existe.");
                }

                // Crear directorio de imágenes
                $directorio = __DIR__ . "/uploads/anuncios/";

                if (!is_dir($directorio) && !mkdir($directorio, 0775, true)) {
                    throw new Exception("No se pudo crear la carpeta de fotografías.");
                }

                // Iniciar transacción
                $conn->begin_transaction();
                $transaccionIniciada = true;

                // ==========================================
                // FOTO PRINCIPAL
                // ==========================================

                $fotoPrincipal = null;

                if (
                    isset($_FILES["foto"]) &&
                    $_FILES["foto"]["error"] !== UPLOAD_ERR_NO_FILE
                ) {

                    if ($_FILES["foto"]["error"] !== UPLOAD_ERR_OK) {
                        throw new Exception("Error al subir la fotografía principal.");
                    }

                    if ($_FILES["foto"]["size"] > 5 * 1024 * 1024) {
                        throw new Exception("La fotografía principal supera los 5 MB.");
                    }

                    $mime = mime_content_type($_FILES["foto"]["tmp_name"]);

                    $extensiones = [
                        "image/jpeg" => "jpg",
                        "image/png" => "png",
                        "image/webp" => "webp",
                        "image/gif" => "gif"
                    ];

                    if (!isset($extensiones[$mime])) {
                        throw new Exception("Formato de fotografía principal no permitido.");
                    }

                    $nombreArchivo = bin2hex(random_bytes(16)) . "." . $extensiones[$mime];
                    $ruta = $directorio . $nombreArchivo;

                    if (!move_uploaded_file($_FILES["foto"]["tmp_name"], $ruta)) {
                        throw new Exception("No se pudo guardar la fotografía principal.");
                    }

                    $fotoPrincipal = "uploads/anuncios/" . $nombreArchivo;
                    $archivosGuardados[] = $ruta;
                }

                // ==========================================
                // DESCRIPCIÓN Y CARACTERÍSTICAS
                // ==========================================

                $texto = $descripcion;

                if ($caracteristicas !== "") {
                    $texto .= "\n\nCaracterísticas: " . $caracteristicas;
                }

                // ==========================================
                // INSERTAR ANUNCIO
                // ==========================================

                $sql = "
                    INSERT INTO Anuncios
                    (
                        TAnuncio,
                        TVivienda,
                        FPrincipal,
                        Alternativo,
                        Titulo,
                        Precio,
                        Texto,
                        Ciudad,
                        Pais,
                        Usuario
                    )
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ";

                $stmt = $conn->prepare($sql);

                $precio = (float)$precio;

                $stmt->bind_param(
                    "iisssdssii",
                    $tipoAnuncio,
                    $tipoVivienda,
                    $fotoPrincipal,
                    $alternativo,
                    $titulo,
                    $precio,
                    $texto,
                    $ciudad,
                    $pais,
                    $idUsuario
                );

                if (!$stmt->execute()) {
                    throw new Exception("No se pudo insertar el anuncio.");
                }

                // Obtener ID del anuncio creado
                $idAnuncio = $conn->insert_id;

                $stmt->close();

                // ==========================================
                // INSERTAR FOTOGRAFÍAS ADICIONALES
                // ==========================================

                if (
                    isset($_FILES["miniaturas"]) &&
                    is_array($_FILES["miniaturas"]["name"])
                ) {

                    $sqlFoto = "
                        INSERT INTO Fotos
                        (Titulo, Foto, Alternativo, Anuncio)
                        VALUES (?, ?, ?, ?)
                    ";

                    $stmtFoto = $conn->prepare($sqlFoto);

                    foreach ($_FILES["miniaturas"]["name"] as $i => $nombreOriginal) {

                        if ($_FILES["miniaturas"]["error"][$i] === UPLOAD_ERR_NO_FILE) {
                            continue;
                        }

                        if ($_FILES["miniaturas"]["error"][$i] !== UPLOAD_ERR_OK) {
                            throw new Exception("Error al subir una fotografía adicional.");
                        }

                        $temporal = $_FILES["miniaturas"]["tmp_name"][$i];

                        if ($_FILES["miniaturas"]["size"][$i] > 5 * 1024 * 1024) {
                            throw new Exception("Una fotografía adicional supera los 5 MB.");
                        }

                        $mime = mime_content_type($temporal);

                        if (!isset($extensiones[$mime])) {
                            throw new Exception("Formato de fotografía adicional no permitido.");
                        }

                        $nombreArchivo = bin2hex(random_bytes(16)) . "." . $extensiones[$mime];
                        $ruta = $directorio . $nombreArchivo;

                        if (!move_uploaded_file($temporal, $ruta)) {
                            throw new Exception("No se pudo guardar una fotografía adicional.");
                        }

                        $archivosGuardados[] = $ruta;

                        $rutaBD = "uploads/anuncios/" . $nombreArchivo;

                        $stmtFoto->bind_param(
                            "sssi",
                            $titulo,
                            $rutaBD,
                            $alternativoFotos,
                            $idAnuncio
                        );

                        if (!$stmtFoto->execute()) {
                            throw new Exception("Error al insertar una fotografía adicional.");
                        }
                    }

                    $stmtFoto->close();
                }

                // Confirmar las inserciones
                $conn->commit();
                $transaccionIniciada = false;

                $mensaje = "¡Anuncio publicado correctamente!";
                $exito = true;

            } catch (Throwable $e) {

                if ($transaccionIniciada) {
                    $conn->rollback();
                }

                // Eliminar las imágenes si ha fallado la operación
                foreach ($archivosGuardados as $archivo) {
                    if (file_exists($archivo)) {
                        unlink($archivo);
                    }
                }

                $mensaje = "Error al publicar el anuncio: " . $e->getMessage();
            }
        }
    }
}

// ==========================================
// CONSULTAS PARA LOS DESPLEGABLES
// ==========================================

$resultado = $conn->query("SELECT * FROM Paises ORDER BY NomPais");
$resultado1 = $conn->query("SELECT * FROM TiposViviendas ORDER BY NomTVivienda");
$resultado2 = $conn->query("SELECT * FROM TiposAnuncios ORDER BY NomTAnuncio");

if (!$resultado || !$resultado1 || !$resultado2) {
    die("Error al cargar los datos: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Crear anuncio</title>

    <link rel="stylesheet" href="style/barraNav.css">
</head>

<body>

    <nav>
        <ul>
            <li>
                <a href="partePrivada.php" id="rgr">
                    <span>Perfil (<?= htmlspecialchars(obtenerSaludo()) ?>)</span>
                </a>
            </li>

            <li>
                <a href="crearAnuncio.php?salir=1" id="lgn">
                    <span>Salir</span>
                </a>
            </li>
        </ul>
    </nav>

    <?php mostrarBarraEstilos(); ?>

    <h2>Crear anuncio</h2>

    <?php if ($mensaje !== "") { ?>
        <p style="color: <?= $exito ? 'green' : 'red' ?>;">
            <?= htmlspecialchars($mensaje) ?>
        </p>
    <?php } ?>

    <form action="crearAnuncio.php" method="POST" enctype="multipart/form-data">

        <label>
            Tipo de anuncio:
            <select name="tipoAnuncio" required>
                <option value="">Selecciona un tipo de anuncio</option>

                <?php while ($valores2 = $resultado2->fetch_assoc()) { ?>
                    <option value="<?= (int)$valores2['IdTAnuncio'] ?>">
                        <?= htmlspecialchars($valores2['NomTAnuncio']) ?>
                    </option>
                <?php } ?>

            </select>
        </label>

        <br><br>

        <label>
            Tipo de vivienda:
            <select name="tipoVivienda" required>
                <option value="">Selecciona un tipo de vivienda</option>

                <?php while ($valores1 = $resultado1->fetch_assoc()) { ?>
                    <option value="<?= (int)$valores1['IdTVivienda'] ?>">
                        <?= htmlspecialchars($valores1['NomTVivienda']) ?>
                    </option>
                <?php } ?>

            </select>
        </label>

        <br><br>

        <label>
            Foto principal:
            <input type="file" name="foto" accept="image/jpeg,image/png,image/webp,image/gif">
        </label>

        <br><br>

        <label>
            Texto alternativo de la foto principal:
            <input type="text" name="alternativo" minlength="10" maxlength="255" required>
        </label>

        <br><br>

        <label>
            Título:
            <input type="text" name="titulo" maxlength="255" required>
        </label>

        <br><br>

        <label>
            Precio:
            <input type="number" name="precio" step="0.01" min="0" required>
        </label>

        <br><br>

        <label>
            Descripción:
            <textarea name="descripcion"></textarea>
        </label>

        <br><br>

        <label>
            Ciudad:
            <input type="text" name="ciudad" maxlength="150">
        </label>

        <br><br>

        <label>
            País:
            <select name="pais" required>
                <option value="">Selecciona un país</option>

                <?php while ($valores = $resultado->fetch_assoc()) { ?>
                    <option value="<?= (int)$valores['IdPais'] ?>">
                        <?= htmlspecialchars($valores['NomPais']) ?>
                    </option>
                <?php } ?>

            </select>
        </label>

        <br><br>

        <label>
            Características:
            <input type="text" name="caracteristicas">
        </label>

        <br><br>

        <label>
            Fotografías adicionales:
            <input type="file"
                   name="miniaturas[]"
                   accept="image/jpeg,image/png,image/webp,image/gif"
                   multiple>
        </label>

        <br><br>

        <label>
            Texto alternativo de las fotografías adicionales:
            <input type="text" name="alternativoFotos" minlength="10" maxlength="255">
        </label>

        <br><br>

        <button type="submit">Publicar anuncio</button>

    </form>

</body>
</html>

<?php
$resultado->free();
$resultado1->free();
$resultado2->free();

$conn->close();
?>
