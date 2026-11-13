<?php
require_once "permitidos.php";
protegerRuta();

$mensajeEnviado = false;
$textoPregunta = "";
$tipoPregunta = "";
$errorPregunta = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $textoPregunta = trim($_POST["textarea1"] ?? "");
    $tipoPregunta = trim($_POST["tipo-pregunta"] ?? "");
    if ($textoPregunta === "" || $tipoPregunta === "") {
        $errorPregunta = "Debes seleccionar un tipo de pregunta y escribir tu mensaje.";
    } else {
        $mensajeEnviado = true;
    }
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
            <h3>Mensaje enviado correctamente</h3>
            <p><strong>Usuario:</strong> <?= htmlspecialchars($_SESSION["usuario"]) ?></p>
            <p><strong>Tipo de pregunta:</strong> <?= htmlspecialchars($tipoPregunta) ?></p>
            <p><strong>Mensaje:</strong> <?= nl2br(htmlspecialchars($textoPregunta)) ?></p>
            <p><a href="index2.php">Volver al inicio</a></p>
        </section>
    <?php else: ?>
        <?php if ($errorPregunta !== ""): ?>
            <p style="color: red; font-weight: bold;"><?= htmlspecialchars($errorPregunta) ?></p>
        <?php endif; ?>
        <form action="pregunta.php" method="POST">
            <h3>Tipo de pregunta:</h3>
            <div>
                <input type="radio" id="info" name="tipo-pregunta" value="Más información">
                <label for="info">Más información</label><br>
                <input type="radio" id="cita" name="tipo-pregunta" value="Solicitar cita">
                <label for="cita">Solicitar cita</label><br>
                <input type="radio" id="oferta" name="tipo-pregunta" value="Comunicar una oferta">
                <label for="oferta">Comunicar una oferta</label>
            </div>
            <div>
                <label for="textarea1">Tu pregunta:</label><br>
                <textarea name="textarea1" id="textarea1" rows="4" cols="50" placeholder="Escribe tu pregunta..."></textarea>
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