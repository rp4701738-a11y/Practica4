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
    <title>Mis anuncios</title>
    <link rel="stylesheet" href="style/partePrivada.css">
    <link rel="stylesheet" href="style/barraNav.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="index2.php" id="rgr"><span>Menú principal</span></a></li>
            <li><a href="partePrivada.php" id="rgr"><span>Perfil (<?= obtenerSaludo() ?>)</span></a></li>
            <li><a href="misAnuncios.php?salir=1" id="lgn"><span>Salir</span></a></li>
        </ul>
    </nav>

    <?php mostrarBarraEstilos(); ?>

    <?php foreach ($catalogoAnuncios as $anuncio): ?>
        <section>
            <a href="verAnuncio.php?id=<?= (int)$anuncio['id'] ?>">
                <img src="<?= htmlspecialchars($anuncio['imagen']) ?>" width="50" height="50" alt="<?= htmlspecialchars($anuncio['titulo']) ?>">
            </a>
            <h3><?= htmlspecialchars($anuncio['titulo']) ?></h3>
            <p>Ciudad: <?= htmlspecialchars($anuncio['ciudad']) ?></p>
            <p>País: <?= htmlspecialchars($anuncio['pais']) ?></p>
            <p>Precio: <?= htmlspecialchars($anuncio['precio']) ?></p>
            <a href="anadirFoto.php?anuncio=<?= urlencode($anuncio['titulo']) ?>">Añadir foto</a>
        </section>
    <?php endforeach; ?>
</body>
</html>
