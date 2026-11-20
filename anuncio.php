<?php
require_once "permitidos.php";
require_once "conexion.php";

protegerRuta();

$enlaceInicio = isset($_SESSION["usuario"]) ? "index2.php" : "index.php";
$idParam = $_GET['id'] ?? null;
$id = filter_var($idParam, FILTER_VALIDATE_INT);

$anuncioActual = null;
$fotos = [];
$errorMensaje = "";
$idUsuarioActual = isset($_SESSION["idUsuario"]) ? (int)$_SESSION["idUsuario"] : 0;

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = null;
$stmt = null;
$stmtFotos = null;
$stmtUsr = null;

if ($id === false || $id <= 0) {
    $errorMensaje = "El identificador del anuncio no es válido.";
} else {
    try {
        $conn = OpenConnection();

        if ($idUsuarioActual <= 0 && !empty($_SESSION["usuario"])) {
            $stmtUsr = $conn->prepare("SELECT IdUsuario FROM Usuarios WHERE NomUsuario = ?");
            $stmtUsr->bind_param("s", $_SESSION["usuario"]);
            $stmtUsr->execute();
            $resUsr = $stmtUsr->get_result();
            if ($filaUsr = $resUsr->fetch_assoc()) {
                $idUsuarioActual = (int)$filaUsr["IdUsuario"];
            }
            $resUsr->close();
        }

        $sql = "SELECT
                    A.IdAnuncio,
                    A.Titulo,
                    A.FPrincipal,
                    A.Alternativo,
                    A.Texto,
                    A.Precio,
                    A.Ciudad,
                    A.Superficie,
                    A.NHabitaciones,
                    A.NBanyos,
                    A.Planta,
                    A.Anyo,
                    A.FRegistro,
                    TA.NomTAnuncio AS TipoAnuncio,
                    TV.NomTVivienda AS TipoVivienda,
                    P.NomPais AS Pais,
                    U.IdUsuario AS IdPropietario,
                    U.NomUsuario AS Usuario
                FROM Anuncios A
                INNER JOIN TiposAnuncios TA ON A.TAnuncio = TA.IdTAnuncio
                INNER JOIN TiposViviendas TV ON A.TVivienda = TV.IdTVivienda
                LEFT JOIN Paises P ON A.Pais = P.IdPais
                INNER JOIN Usuarios U ON A.Usuario = U.IdUsuario
                WHERE A.IdAnuncio = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $anuncioActual = $res->fetch_assoc();
        $res->close();

        if ($anuncioActual === null) {
            $errorMensaje = "No se ha encontrado el anuncio solicitado.";
        } else {
            $stmtFotos = $conn->prepare("SELECT IdFoto, Titulo, Foto, Alternativo FROM Fotos WHERE Anuncio = ? ORDER BY IdFoto ASC");
            $stmtFotos->bind_param("i", $id);
            $stmtFotos->execute();
            $resFotos = $stmtFotos->get_result();
            while ($f = $resFotos->fetch_assoc()) {
                $fotos[] = $f;
            }
            $resFotos->close();

            registrarVisitaAnuncio($id, $catalogoAnuncios);
        }
    } catch (mysqli_sql_exception $e) {
        error_log("Error en anuncio.php: " . $e->getMessage());
        $errorMensaje = "Error al obtener los datos del anuncio.";
    } finally {
        if ($stmtUsr instanceof mysqli_stmt) {
            $stmtUsr->close();
        }
        if ($stmtFotos instanceof mysqli_stmt) {
            $stmtFotos->close();
        }
        if ($stmt instanceof mysqli_stmt) {
            $stmt->close();
        }
        if ($conn instanceof mysqli) {
            $conn->close();
        }
    }
}

session_write_close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $anuncioActual ? htmlspecialchars((string)$anuncioActual['Titulo'], ENT_QUOTES, 'UTF-8') : 'Detalle de anuncio' ?></title>
    <link rel="stylesheet" href="style/anuncio.css">
    <link rel="stylesheet" href="style/barraNav.css">
</head>
<body>
    <h3>URL: https://buscasa.gt.tc/anuncio.php</h3>
    <nav>
        <ul>
            <li><a href="<?= $enlaceInicio ?>" id="rgr"><span>Menú principal</span></a></li>
            <li><a href="busqueda.php" id="rgr"><span>Búsqueda</span></a></li>
            <li><a href="partePrivada.php" id="rgr"><span>Perfil (<?= obtenerSaludo() ?>)</span></a></li>
            <li><a href="anuncio.php?salir=1" id="lgn"><span>Salir</span></a></li>
        </ul>
    </nav>

    <?php mostrarBarraEstilos(); ?>

    <input type="checkbox" id="print-toggle" style="display: none;">
    <label for="print-toggle" style="display: block; margin-bottom: 10px; color: black; cursor: pointer; clear: both;">
        <u>Cambiar vista normal/impresión</u>
    </label>

    <?php if ($errorMensaje !== ""): ?>
        <section>
            <p style="color: red; font-weight: bold;"><?= htmlspecialchars($errorMensaje, ENT_QUOTES, 'UTF-8') ?></p>
            <p><a href="busqueda.php">Volver a la búsqueda</a></p>
        </section>
    <?php else: ?>
        <section>
            <form action="pregunta.php" method="GET" class="lo">
                <input type="hidden" name="id" value="<?= (int)$anuncioActual['IdAnuncio'] ?>">
                <button type="submit">Preguntar</button>
            </form>

            <h2><?= htmlspecialchars((string)$anuncioActual['Titulo'], ENT_QUOTES, 'UTF-8') ?></h2>

            <?php if (!empty($anuncioActual['FPrincipal'])): ?>
                <br>
                <a href="verFotos.php?id=<?= (int)$anuncioActual['IdAnuncio'] ?>">
                    <img src="<?= htmlspecialchars((string)$anuncioActual['FPrincipal'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars((string)$anuncioActual['Alternativo'], ENT_QUOTES, 'UTF-8') ?>" style="max-width: 500px; width: 100%; height: auto;">
                </a>
            <?php endif; ?>

            <p><strong>Tipo de anuncio:</strong> <?= htmlspecialchars((string)$anuncioActual['TipoAnuncio'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>Tipo de vivienda:</strong> <?= htmlspecialchars((string)$anuncioActual['TipoVivienda'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>Descripción:</strong> <?= nl2br(htmlspecialchars((string)($anuncioActual['Texto'] ?? ''), ENT_QUOTES, 'UTF-8')) ?></p>
            <p><strong>Fecha de publicación:</strong> <?= htmlspecialchars(date('d/m/Y H:i', strtotime((string)$anuncioActual['FRegistro'])), ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>Ciudad:</strong> <?= htmlspecialchars((string)($anuncioActual['Ciudad'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>País:</strong> <?= htmlspecialchars((string)($anuncioActual['Pais'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>Precio:</strong> <?= htmlspecialchars(number_format((float)$anuncioActual['Precio'], 2, ',', '.'), ENT_QUOTES, 'UTF-8') ?> €</p>

            <h3>Características</h3>
            <ul>
                <li><strong>Superficie:</strong> <?= htmlspecialchars((string)($anuncioActual['Superficie'] ?? ''), ENT_QUOTES, 'UTF-8') ?> m²</li>
                <li><strong>Habitaciones:</strong> <?= htmlspecialchars((string)($anuncioActual['NHabitaciones'] ?? ''), ENT_QUOTES, 'UTF-8') ?></li>
                <li><strong>Baños:</strong> <?= htmlspecialchars((string)($anuncioActual['NBanyos'] ?? ''), ENT_QUOTES, 'UTF-8') ?></li>
                <li><strong>Planta:</strong> <?= htmlspecialchars((string)($anuncioActual['Planta'] ?? ''), ENT_QUOTES, 'UTF-8') ?></li>
                <li><strong>Año de construcción:</strong> <?= htmlspecialchars((string)($anuncioActual['Anyo'] ?? ''), ENT_QUOTES, 'UTF-8') ?></li>
            </ul>

            <p>
                <strong>Publicado por:</strong>
                <a href="perfil.php?id=<?= (int)$anuncioActual['IdPropietario'] ?>">
                    <?= htmlspecialchars((string)$anuncioActual['Usuario'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            </p>

            <h3>Fotos del anuncio (<?= count($fotos) ?>)</h3>
            <?php if (count($fotos) > 0): ?>
                <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 10px;">
                    <?php foreach ($fotos as $foto): ?>
                        <a href="verFotos.php?id=<?= (int)$anuncioActual['IdAnuncio'] ?>">
                            <img src="<?= htmlspecialchars((string)$foto['Foto'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars((string)$foto['Alternativo'], ENT_QUOTES, 'UTF-8') ?>" style="width: 120px; height: 90px; object-fit: cover;">
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>Este anuncio no tiene fotos adicionales.</p>
            <?php endif; ?>

            <p>
                <a href="verFotos.php?id=<?= (int)$anuncioActual['IdAnuncio'] ?>">
                    <button type="button">Ver todas las fotos</button>
                </a>
                <?php if ($idUsuarioActual === (int)$anuncioActual['IdPropietario']): ?>
                    <a href="mensajesAnuncio.php?id=<?= (int)$anuncioActual['IdAnuncio'] ?>">
                        <button type="button">Ver mensajes de este anuncio</button>
                    </a>
                <?php endif; ?>
            </p>
        </section>
    <?php endif; ?>

    <?php mostrarPanelUltimosAnuncios($catalogoAnuncios); ?>

    <footer>
        <br><br>
        <hr size="1px" align="center" color="black">
        <p>© 2026. Aviso de copyright: Contenido de uso libre.</p>
    </footer>
</body>
</html>