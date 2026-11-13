<?php
require_once "permitidos.php";
session_write_close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Búsqueda</title>
    <link rel="stylesheet" href="style/barraNav.css">
    <link rel="stylesheet" href="style/busqueda.css">
    <link rel="stylesheet" href="style/titulos.css">
</head>
<body>
    <h3>URL: https://buscasa.gt.tc/busqueda.php</h3>
    <ul id="menu-principal">
        <li><a href="index.php" id="rgr"><span>Menú principal</span></a></li>
        <?php if (!isset($_SESSION["usuario"])): ?>
            <li><a href="signIn.php" id="lgn"><span>Iniciar sesión</span></a></li>
            <li><a href="signUp.php" id="lgn"><span>Registrarse</span></a></li>
        <?php else: ?>
            <li><a href="partePrivada.php" id="rgr"><span>Perfil (<?= obtenerSaludo() ?>)</span></a></li>
            <li><a href="busqueda.php?salir=1" id="lgn"><span>Salir</span></a></li>
        <?php endif; ?>
    </ul>

    <?php mostrarBarraEstilos(); ?>

    <input type="checkbox" id="print-toggle" style="display: none;">
    <label for="print-toggle" style="display: block; margin-bottom: 10px; color: black; cursor: pointer; clear: both;">
        <u>Cambiar vista normal/impresión</u>
    </label>

    <h2>Búsqueda avanzada</h2>
    <hr size="3" color="black">

    <form action="resultadoBusqueda.php" method="GET">
        <section>
            <label>Tipo de venta:</label>
            <input type="radio" id="venta" name="tipo-venta" value="Venta">
            <label for="venta">Venta</label>
            <input type="radio" id="alquiler" name="tipo-venta" value="Alquiler">
            <label for="alquiler">Alquiler</label><br>
        </section>
        <section>
            <label>Tipo de vivienda: <input type="text" name="tipo-vivienda"></label>
        </section>
        <section>
            <label for="country">País:</label>
            <input list="country" name="country" class="datalist-input" />
            <datalist id="country">
                <option value="Spain">
                <option value="Portugal">
                <option value="France">
                <option value="Italy">
                <option value="Germany">
                <option value="United Kingdom">
                <option value="United States">
                <option value="Argentina">
                <option value="Mexico">
            </datalist><br>
            <label>Ciudad: <input type="text" name="ciudad"></label>
        </section>
        <section id="boton-enviar">
            <input type="submit" value="🔍">
        </section>
    </form>

    <?php mostrarPanelUltimosAnuncios($catalogoAnuncios); ?>

    <footer>
        <br><br>
        <hr size="1px" align="center" color="black">
        <p>© 2026. Aviso de copyright: Contenido de uso libre.</p>
    </footer>
</body>
</html>