<?php
require_once "permitidos.php";

$ciudad = $_GET['ciudad'] ?? '';
$resultados = [];
foreach ($catalogoAnuncios as $anuncio) {
    if ($ciudad === '' || strcasecmp($anuncio['ciudad'], trim($ciudad)) === 0) {
        $resultados[] = $anuncio;
    }
}
session_write_close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado</title>
    <link rel="stylesheet" href="style/sectionP.css">
    <link rel="stylesheet" href="style/barraNav.css">
    <link rel="stylesheet" href="style/resultadoBusqueda.css">
</head>
<body>
    <h3 id="url-impresion">URL: https://buscasa.gt.tc/resultadoBusqueda.php</h3>
    <nav id="menu-principal">
        <ul>
            <li><a href="index.php" id="rgr"><span>Menú principal</span></a></li>
            <li><a href="busqueda.php" id="rgr"><span>Búsqueda</span></a></li>
            <?php if (isset($_SESSION["usuario"])): ?>
                <li><a href="partePrivada.php" id="rgr"><span>Perfil (<?= obtenerSaludo() ?>)</span></a></li>
                <li><a href="resultadoBusqueda.php?salir=1" id="lgn"><span>Salir</span></a></li>
            <?php else: ?>
                <li><a href="signIn.php" id="lgn"><span>Iniciar sesión</span></a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <?php mostrarBarraEstilos(); ?>

    <input type="checkbox" id="print-toggle" style="display: none;">
    <label for="print-toggle" style="display: block; margin-bottom: 10px; color: black; cursor: pointer; clear: both;">
        <u>Cambiar vista normal/impresión</u>
    </label>

    <h2>Resultados de la búsqueda</h2>
    <hr size="1px" align="left" color="black">
    <br><br>
    <?php if (count($resultados) > 0): ?>
        <?php foreach ($resultados as $anuncio): ?>
            <section>
                <a href="anuncio.php?id=<?= (int)$anuncio['id'] ?>">
                    <img src="<?= htmlspecialchars($anuncio['imagen']) ?>" alt="<?= htmlspecialchars($anuncio['titulo']) ?>">
                </a>
                <p><?= htmlspecialchars($anuncio['titulo']) ?></p>
                <p><?= htmlspecialchars($anuncio['fecha']) ?></p>
                <p><?= htmlspecialchars($anuncio['ciudad']) ?></p>
                <p><?= htmlspecialchars($anuncio['pais']) ?></p>
                <p><?= htmlspecialchars($anuncio['precio']) ?></p>
            </section>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No se han encontrado anuncios en <?= htmlspecialchars($ciudad) ?>.</p>
    <?php endif; ?>

    <?php mostrarPanelUltimosAnuncios($catalogoAnuncios); ?>

    <footer>
        <br><br>
        <hr size="1px" align="center" color="black">
        <p>© 2026. Aviso de copyright: Contenido de uso libre.</p>
    </footer>
</body>
</html>