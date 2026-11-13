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
    <title>Principal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style/index2.css">
    <link rel="stylesheet" href="style/sectionP.css">
    <link rel="stylesheet" href="style/barraNav.css">
</head>
<body>
    <h3>URL: https://buscasa.gt.tc/index2.php</h3>
    <nav>
        <ul>
            <li><a href="busqueda.php" id="rgr"><span><i class="fa-solid fa-magnifying-glass"></i> Búsqueda</span></a></li>
            <li><a href="partePrivada.php" id="rgr"><span>Perfil (<?= obtenerSaludo() ?>)</span></a></li>
            <li><a href="index2.php?salir=1" id="lgn"><span>Salir</span></a></li>
        </ul>
    </nav>

    <?php mostrarBarraEstilos(); ?>

    <div>
        <form action="resultadoBusqueda.php" method="GET">
            <input type="text" placeholder="Buscar por ciudad" id="buscador" name="ciudad">
            <input type="submit" value="🔍">
        </form><br>

        <input type="checkbox" id="print-toggle" style="display: none;">
        <label for="print-toggle" style="display: block; margin-bottom: 10px; color: black; cursor: pointer;">
            <u>Cambiar vista normal/impresión</u>
        </label>
        <br><br>
        <h2>Anuncios destacados</h2>
        <hr size="1px" align="left" color="black">
        <br><br>
        <?php foreach ($catalogoAnuncios as $anuncio): ?>
            <section>
                <a href="anuncio.php?id=<?= (int)$anuncio['id'] ?>">
                    <img src="<?= htmlspecialchars($anuncio['imagen']) ?>" alt="<?= htmlspecialchars($anuncio['titulo']) ?>">
                </a>
                <p><?= htmlspecialchars($anuncio['titulo']) ?></p>
            </section>
        <?php endforeach; ?>

        <?php mostrarPanelUltimosAnuncios($catalogoAnuncios); ?>
    </div>

    <footer>
        <br><br>
        <hr size="1px" align="center" color="black">
        <p>© 2026. Aviso de copyright: Contenido de uso libre.</p>
    </footer>
</body>
</html>