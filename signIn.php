<?php
require_once "permitidos.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST" && empty($_COOKIE["recordar_token"])) {
    unset(
        $_SESSION["usuario"],
        $_SESSION["nombre"],
        $_SESSION["estilos"],
        $_SESSION["estilo"],
        $_SESSION["recordado"],
        $_SESSION["ultima_visita"]
    );
}

$errorFlash = obtenerFlash("error");
$esRecordado = !empty($_COOKIE["recordar_token"]) && isset($_SESSION["usuario"]);
session_write_close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link rel="stylesheet" href="style/signUp.css">
    <link rel="stylesheet" href="style/barraNav.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <h3 id="url-impresion">URL: https://buscasa.gt.tc/signIn.php</h3>
    <nav>
        <ul>
            <li><a href="index.php" id="rgr"><span>Menú principal</span></a></li>
            <li><a href="busqueda.php" id="rgr"><span><i class="fa-solid fa-magnifying-glass"></i> Búsqueda</span></a></li>
            <?php if ($esRecordado): ?>
                <li><a href="partePrivada.php" id="rgr"><span>Perfil (<?= obtenerSaludo() ?>)</span></a></li>
                <li><a href="signIn.php?salir=1" id="lgn"><span>Salir</span></a></li>
            <?php else: ?>
                <li><a href="partePrivada.php" id="rgr"><span>Perfil</span></a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <?php mostrarBarraEstilos(); ?>

    <input type="checkbox" id="print-toggle" style="display: none;">
    <label for="print-toggle" style="display: block; margin-bottom: 10px; color: black; cursor: pointer; clear: both;">
        <u>Cambiar vista normal/impresión</u>
    </label>

    <section id="signIn">
        <?php if ($esRecordado): ?>
            <h1>Sesión recordada</h1>
            <p>
                Hola <strong><?= htmlspecialchars($_SESSION["nombre"] ?? $_SESSION["usuario"]) ?></strong>,
                su última visita fue el <strong><?= htmlspecialchars($_SESSION["ultima_visita"] ?? date("d/m/y \a \l\a\s H:i")) ?></strong>
            </p>
            <form action="partePrivada.php" method="GET" style="display:inline;">
                <button type="submit">Acceder</button>
            </form>
            <form action="signIn.php" method="POST" style="display:inline;">
                <button type="submit" name="salir">Salir</button>
            </form>
        <?php else: ?>
            <h1>Iniciar sesión:</h1>
            <?php if ($errorFlash !== null): ?>
                <p style="color: red; font-weight: bold;"><?= htmlspecialchars($errorFlash) ?></p>
            <?php endif; ?>
            <form action="controlAcc.php" method="POST" novalidate>
                <input type="hidden" name="origen" value="signIn">
                <label>Nombre de usuario: <input type="text" id="nu" name="loginUser"></label>
                <br><br><label>Contraseña: <input type="password" id="ct" name="loginPass"></label>
                <br><br><label><input type="checkbox" name="recordarme" value="1"> Recordarme en este equipo</label>
                <br><br><input type="submit" value="Acceder">
            </form>
            <p>¿No tienes una cuenta? <a href="signUp.php">Regístrate</a></p>
        <?php endif; ?>
    </section>

    <?php mostrarPanelUltimosAnuncios($catalogoAnuncios); ?>

    <footer>
        <br><br>
        <hr size="1px" align="center" color="black">
        <p>&copy; 2026. Aviso de copyright: Contenido de uso libre.</p>
    </footer>
</body>
</html>
