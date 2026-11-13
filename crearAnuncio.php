<?php
require_once "permitidos.php";
protegerRuta();
session_write_close();
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
            <li><a href="partePrivada.php" id="rgr"><span>Perfil (<?= obtenerSaludo() ?>)</span></a></li>
            <li><a href="crearAnuncio.php?salir=1" id="lgn"><span>Salir</span></a></li>
        </ul>
    </nav>

    <?php mostrarBarraEstilos(); ?>

    <form action="">
        <label>Tipo de anuncio: <input type="text"></label><br>
        <label>Tipo de vivienda: <input type="text"></label><br>
        <label>Foto: <input type="file"></label><br>
        <label>Título: <input type="text"></label><br>
        <label>Precio: <input type="number"></label><br>
        <label>Descripción: <input type="text"></label><br>
        <label>Fecha: <input type="date"></label><br>
        <label>Ciudad: <input type="text"></label><br>
        <label>País: <input type="text"></label><br>
        <label>Características: <input type="text"></label><br>
        <label>Miniatura de fotos: <input type="file"></label>
    </form>
</body>
</html>
