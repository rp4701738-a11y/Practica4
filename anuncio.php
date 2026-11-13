<?php
require_once "permitidos.php";
protegerRuta();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 1;
if (!isset($catalogoAnuncios[$id])) {
    $id = ($id % 2 === 0) ? 2 : 1;
}
$anuncioActual = $catalogoAnuncios[$id];

registrarVisitaAnuncio($id, $catalogoAnuncios);
session_write_close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($anuncioActual['titulo']) ?></title>
    <link rel="stylesheet" href="style/anuncio.css">
    <link rel="stylesheet" href="style/barraNav.css">
</head>
<body>
    <h3>URL: https://buscasa.gt.tc/anuncio.php</h3>
    <nav>
        <ul>
            <li><a href="index.php" id="rgr"><span>Menú principal</span></a></li>
            <li><a href="busqueda.php" id="rgr"><span>Búsqueda</span></a></li>
            <li><a href="partePrivada.php" id="rgr"><span>Perfil (<?= obtenerSaludo() ?>)</span></a></li>
            <li><a href="anuncio.php?salir=1" id="lgn"><span>Salir</span></a></li>
        </ul>
    </nav>

    <?php mostrarBarraEstilos(); ?>

    <input type="checkbox" id="print-toggle" style="display: none;">
    <label for="print-toggle" style="display: block; margin-bottom: 10px; color: black; cursor: pointer; clear: both;">
        <u>Cambiar vista normal/impresión</u>
    </label>

    <section>
        <form action="pregunta.php" method="GET" class="lo">
            <input type="hidden" name="id" value="<?= (int)$anuncioActual['id'] ?>">
            <button type="submit">Preguntar</button>
        </form>

        <br><img src="<?= htmlspecialchars($anuncioActual['imagen']) ?>" alt="<?= htmlspecialchars($anuncioActual['titulo']) ?>">
        <p><strong><?= htmlspecialchars($anuncioActual['titulo']) ?></strong></p>
        <p><?= htmlspecialchars($anuncioActual['fecha']) ?></p>
        <p>Ciudad: <?= htmlspecialchars($anuncioActual['ciudad']) ?></p>
        <p>País: <?= htmlspecialchars($anuncioActual['pais']) ?></p>
        <p>Precio: <?= htmlspecialchars($anuncioActual['precio']) ?></p>
    </section>

    <?php mostrarPanelUltimosAnuncios($catalogoAnuncios); ?>

    <footer>
        <br><br>
        <hr size="1px" align="center" color="black">
        <p>© 2026. Aviso de copyright: Contenido de uso libre.</p>
    </footer>
</body>
</html>