<?php
require_once "permitidos.php";
require_once "conexion.php";
require_once "selectPaises.php";

$erroresFlash = obtenerFlash("errores_registro");
$datosFormulario = obtenerFlash("datos_registro") ?? [];
$enlaceInicio = isset($_SESSION["usuario"]) ? "index2.php" : "index.php";
$paises = [];
$errorBD = "";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = null;
try {
    $conn = OpenConnection();
    $paises = obtenerPaises($conn);
} catch (mysqli_sql_exception $e) {
    error_log("Error en signUp.php: " . $e->getMessage());
    $errorBD = "No se pudo cargar la lista de países en este momento.";
} finally {
    if ($conn instanceof mysqli) {
        $conn->close();
    }
}

$actionFormulario = "signUpRespuesta.php";
$textoBoton = "Enviar";
session_write_close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="style/signUp.css">
    <link rel="stylesheet" href="style/barraNav.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="javascript/signUp.js"></script>
    <style>
        .error-msg { color: red; font-size: 0.9em; display: none; margin-left: 10px; }
        .input-error { border: 2px solid red; background-color: #ffe6e6; }
    </style>
</head>
<body>
    <h3 id="url-impresion">URL: https://buscasa.gt.tc/signUp.php</h3>
    <nav>
        <ul>
            <li><a href="<?= $enlaceInicio ?>" id="rgr"><span>Menú principal</span></a></li>
            <li><a href="busqueda.php" id="rgr"><span><i class="fa-solid fa-magnifying-glass"></i> Búsqueda</span></a></li>
            <?php if (isset($_SESSION["usuario"])): ?>
                <li><a href="partePrivada.php" id="rgr"><span>Perfil</span></a></li>
                <li><a href="signUp.php?salir=1" id="lgn"><span>Salir</span></a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <?php mostrarBarraEstilos(); ?>

    <input type="checkbox" id="print-toggle" style="display: none;">
    <label for="print-toggle" style="display: block; margin-bottom: 10px; color: black; cursor: pointer; clear: both;">
        <u>Cambiar vista normal/impresión</u>
    </label>

    <section id="signIn">
        <h1>Registro:</h1>

        <?php if ($errorBD !== ""): ?>
            <p style="color: red; font-weight: bold;"><?= htmlspecialchars($errorBD, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <?php if (!empty($erroresFlash)): ?>
            <div style="border: 2px solid red; padding: 12px; background-color: #ffe6e6; margin-bottom: 15px;">
                <p style="color: red; font-weight: bold;">Se han encontrado errores en los datos introducidos:</p>
                <ul>
                    <?php foreach ($erroresFlash as $err): ?>
                        <li style="color: red;"><?= htmlspecialchars((string)$err, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php include "formUsuario.php"; ?>

        <p>¿Ya tienes una cuenta? <a href="signIn.php">Inicia sesión</a></p>
    </section>

    <?php mostrarPanelUltimosAnuncios($catalogoAnuncios); ?>

    <footer>
        <br><br>
        <hr size="1px" align="center" color="black">
        <p>© 2026. Aviso de copyright: Contenido de uso libre.</p>
    </footer>
</body>
</html>