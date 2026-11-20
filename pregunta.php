<?php
require_once "permitidos.php";
require_once "conexion.php";

protegerRuta();

$mensajeEnviado = false;
$textoPregunta = "";
$tipoPreguntaNombre = "";
$tipoPreguntaId = "";
$errorPregunta = "";
$tiposMensajes = [];
$mapaTipos = [];
$tituloAnuncio = "";

$idAnuncioParam = $_POST["id_anuncio"] ?? $_GET["id"] ?? null;
$idAnuncio = null;
if ($idAnuncioParam !== null && $idAnuncioParam !== "") {
    $idValidado = filter_var($idAnuncioParam, FILTER_VALIDATE_INT);
    if ($idValidado === false || $idValidado <= 0) {
        $errorPregunta = "El identificador del anuncio no es válido.";
    } else {
        $idAnuncio = $idValidado;
    }
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = null;
$stmtAnuncio = null;

try {
    $conn = OpenConnection();

    $resTM = $conn->query("SELECT IdTMensaje, NomTMensaje FROM TiposMensajes ORDER BY IdTMensaje ASC");
    while ($fila = $resTM->fetch_assoc()) {
        $tiposMensajes[] = $fila;
        $mapaTipos[(int)$fila['IdTMensaje']] = $fila['NomTMensaje'];
    }
    $resTM->close();

    if ($idAnuncio !== null) {
        $stmtAnuncio = $conn->prepare("SELECT Titulo FROM Anuncios WHERE IdAnuncio = ?");
        $stmtAnuncio->bind_param("i", $idAnuncio);
        $stmtAnuncio->execute();
        $resAn = $stmtAnuncio->get_result();
        if ($filaAn = $resAn->fetch_assoc()) {
            $tituloAnuncio = $filaAn['Titulo'];
        } else {
            $errorPregunta = "El anuncio seleccionado no existe.";
            $idAnuncio = null;
        }
        $resAn->close();
    }
} catch (mysqli_sql_exception $e) {
    error_log("Error en pregunta.php: " . $e->getMessage());
    $errorPregunta = "Error al cargar los tipos de mensaje desde la base de datos.";
} finally {
    if ($stmtAnuncio instanceof mysqli_stmt) {
        $stmtAnuncio->close();
    }
    if ($conn instanceof mysqli) {
        $conn->close();
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_POST["actualizar_estilos"]) && $errorPregunta === "") {
    $textoPregunta = trim((string)($_POST["textarea1"] ?? ""));
    $tipoPreguntaId = trim((string)($_POST["tipo-pregunta"] ?? ""));
    $idTipoInt = filter_var($tipoPreguntaId, FILTER_VALIDATE_INT);

    if ($idTipoInt === false || !isset($mapaTipos[$idTipoInt])) {
        $errorPregunta = "Debes seleccionar un tipo de mensaje válido.";
    } elseif ($textoPregunta === "") {
        $errorPregunta = "Debes escribir el texto de tu mensaje.";
    } elseif (mb_strlen($textoPregunta, 'UTF-8') > 4000) {
        $errorPregunta = "El mensaje no puede superar los 4000 caracteres.";
    } else {
        $mensajeEnviado = true;
        $tipoPreguntaNombre = $mapaTipos[$idTipoInt];
        $_SESSION["ultima_pregunta_ok"] = [
            "texto" => $textoPregunta,
            "tipo" => $tipoPreguntaNombre
        ];
    }
} elseif (isset($_GET["enviado"]) && isset($_SESSION["ultima_pregunta_ok"]) && $_SERVER["REQUEST_METHOD"] === "GET") {
    $mensajeEnviado = true;
    $textoPregunta = $_SESSION["ultima_pregunta_ok"]["texto"];
    $tipoPreguntaNombre = $_SESSION["ultima_pregunta_ok"]["tipo"];
}

session_write_close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pregunta</title>
    <link rel="stylesheet" href="style/barraNav.css">
    <link rel="stylesheet" href="style/titulos.css">
    <link rel="stylesheet" href="style/pregunta.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <h3 id="url-impresion">URL: https://buscasa.gt.tc/pregunta.php</h3>
    <ul id="menu-principal">
        <li><a href="index2.php" id="rgr"><span>Inicio</span></a></li>
        <li><a href="busqueda.php" id="rgr"><span><i class="fa-solid fa-magnifying-glass"></i> Búsqueda</span></a></li>
        <li><a href="partePrivada.php" id="rgr"><span>Perfil (<?= obtenerSaludo() ?>)</span></a></li>
        <li><a href="pregunta.php?salir=1" id="lgn"><span>Salir</span></a></li>
    </ul>

    <?php mostrarBarraEstilos(); ?>

    <input type="checkbox" id="print-toggle" style="display: none;">
    <label for="print-toggle" style="display: block; margin-bottom: 10px; color: black; cursor: pointer; clear: both;">
        <u>Cambiar vista normal/impresión</u>
    </label>

    <?php if ($mensajeEnviado): ?>
        <section style="border: 2px solid green; padding: 15px; background-color: #e6ffe6; margin: 15px;">
            <h3>Mensaje enviado con éxito</h3>
            <?php if ($tituloAnuncio !== ""): ?>
                <p><strong>Anuncio:</strong> <?= htmlspecialchars($tituloAnuncio, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
            <p><strong>Usuario:</strong> <?= htmlspecialchars((string)$_SESSION["usuario"], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>Tipo de mensaje:</strong> <?= htmlspecialchars($tipoPreguntaNombre, ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>Mensaje:</strong> <?= nl2br(htmlspecialchars($textoPregunta, ENT_QUOTES, 'UTF-8')) ?></p>
            <p><a href="index2.php">Volver al inicio</a></p>
        </section>
    <?php else: ?>
        <?php if ($errorPregunta !== ""): ?>
            <p style="color: red; font-weight: bold;"><?= htmlspecialchars($errorPregunta, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <?php if ($tituloAnuncio !== ""): ?>
            <h2>Enviar mensaje sobre: <?= htmlspecialchars($tituloAnuncio, ENT_QUOTES, 'UTF-8') ?></h2>
        <?php endif; ?>

        <form action="pregunta.php" method="POST">
            <?php if ($idAnuncio !== null): ?>
                <input type="hidden" name="id_anuncio" value="<?= (int)$idAnuncio ?>">
            <?php endif; ?>
            <h3><label for="tipo-pregunta">Tipo de mensaje:</label></h3>
            <div>
                <select id="tipo-pregunta" name="tipo-pregunta">
                    <option value="">-- Selecciona un tipo de mensaje --</option>
                    <?php foreach ($tiposMensajes as $tm): ?>
                        <option value="<?= (int)$tm['IdTMensaje'] ?>"<?= ((string)$tipoPreguntaId === (string)$tm['IdTMensaje']) ? ' selected' : '' ?>>
                            <?= htmlspecialchars((string)$tm['NomTMensaje'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="textarea1">Tu mensaje:</label><br>
                <textarea name="textarea1" id="textarea1" rows="4" cols="50" maxlength="4000" placeholder="Escribe tu mensaje..."><?= htmlspecialchars($textoPregunta, ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <section>
                <input type="submit" value="Enviar">
            </section>
        </form>
    <?php endif; ?>

    <?php mostrarPanelUltimosAnuncios($catalogoAnuncios); ?>

    <footer>
        <br><br>
        <hr size="1px" align="center" color="black">
        <p>© 2026. Aviso de copyright: Contenido de uso libre.</p>
    </footer>
</body>
</html>