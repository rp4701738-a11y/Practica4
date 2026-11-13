<?php
require_once "permitidos.php";
protegerRuta();
$anuncio = $_GET['anuncio'] ?? '';
session_write_close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir foto</title>
    <link rel="stylesheet" href="style/barraNav.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="misAnuncios.php" id="rgr"><span>Mis anuncios</span></a></li>
            <li><a href="partePrivada.php" id="rgr"><span>Perfil (<?= obtenerSaludo() ?>)</span></a></li>
            <li><a href="anadirFoto.php?salir=1" id="lgn"><span>Salir</span></a></li>
        </ul>
    </nav>

    <?php mostrarBarraEstilos(); ?>

    <form action="">
        <label>Foto: <input type="file"></label><br>
        <label>Título: <input type="text"></label><br>
        <label>Texto alternativo: <input type="text"></label><br>
        <label>Anuncio al que añadírselo: <input type="text" value="<?= htmlspecialchars($anuncio) ?>"></label>
    </form>
</body>
</html>
