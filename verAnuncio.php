<?php
require_once "permitidos.php";
protegerRuta();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 3;
$anuncio = $catalogoAnuncios[$id] ?? $catalogoAnuncios[3];
session_write_close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver anuncio</title>
    <link rel="stylesheet" href="style/partePrivada.css">
    <link rel="stylesheet" href="style/barraNav.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="misAnuncios.php" id="rgr"><span>Mis anuncios</span></a></li>
            <li><a href="partePrivada.php" id="rgr"><span>Perfil (<?= obtenerSaludo() ?>)</span></a></li>
            <li><a href="verAnuncio.php?salir=1" id="lgn"><span>Salir</span></a></li>
        </ul>
    </nav>

    <?php mostrarBarraEstilos(); ?>

    <section>
        <p>Tipo de anuncio: <?= htmlspecialchars($anuncio['tipo_anuncio']) ?></p>
        <p>Tipo de vivienda: <?= htmlspecialchars($anuncio['tipo_vivienda']) ?></p>
        <img src="<?= htmlspecialchars($anuncio['imagen']) ?>" width="50" height="50" alt="<?= htmlspecialchars($anuncio['titulo']) ?>">
        <p><?= htmlspecialchars($anuncio['descripcion']) ?></p>
        <h3><?= htmlspecialchars($anuncio['titulo']) ?></h3>
        <p>Ciudad: <?= htmlspecialchars($anuncio['ciudad']) ?></p>
        <p>País: <?= htmlspecialchars($anuncio['pais']) ?></p>
        <p>Precio: <?= htmlspecialchars($anuncio['precio']) ?></p>
        <a href="anadirFoto.php?anuncio=<?= urlencode($anuncio['titulo']) ?>">Añadir foto</a>
    </section>
</body>
</html>