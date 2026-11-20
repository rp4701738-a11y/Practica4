<?php
require_once "permitidos.php";
require_once "conexion.php";
require_once "selectPaises.php";

$enlaceInicio = isset($_SESSION["usuario"]) ? "index2.php" : "index.php";
$tiposAnuncios = [];
$tiposViviendas = [];
$paises = [];
$errorBD = "";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = null;
try {
    $conn = OpenConnection();

    $resTA = $conn->query("SELECT IdTAnuncio, NomTAnuncio FROM TiposAnuncios ORDER BY IdTAnuncio ASC");
    while ($fila = $resTA->fetch_assoc()) {
        $tiposAnuncios[] = $fila;
    }
    $resTA->close();

    $resTV = $conn->query("SELECT IdTVivienda, NomTVivienda FROM TiposViviendas ORDER BY IdTVivienda ASC");
    while ($fila = $resTV->fetch_assoc()) {
        $tiposViviendas[] = $fila;
    }
    $resTV->close();

    $paises = obtenerPaises($conn);
} catch (mysqli_sql_exception $e) {
    error_log("Error en busqueda.php: " . $e->getMessage());
    $errorBD = "Error al cargar los filtros desde la base de datos.";
} finally {
    if ($conn instanceof mysqli) {
        $conn->close();
    }
}

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
        <li><a href="<?= $enlaceInicio ?>" id="rgr"><span>Menú principal</span></a></li>
        <?php if (!isset($_SESSION["usuario"])): ?>
            <li><a href="signUp.php" id="lgn"><span>Registrarse</span></a></li>
            <li><a href="partePrivada.php" id="rgr"><span>Perfil</span></a></li>
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

    <?php if ($errorBD !== ""): ?>
        <p style="color: red; font-weight: bold;"><?= htmlspecialchars($errorBD, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form action="resultadoBusqueda.php" method="GET">
        <input type="hidden" name="avanzada" value="1">
        <section>
            <label for="tipo_anuncio">Tipo de anuncio:</label>
            <select id="tipo_anuncio" name="tipo_anuncio">
                <option value="">-- Todos los tipos --</option>
                <?php foreach ($tiposAnuncios as $ta): ?>
                    <option value="<?= (int)$ta['IdTAnuncio'] ?>"><?= htmlspecialchars((string)$ta['NomTAnuncio'], ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
        </section>
        <section>
            <label for="tipo_vivienda">Tipo de vivienda:</label>
            <select id="tipo_vivienda" name="tipo_vivienda">
                <option value="">-- Todos los tipos --</option>
                <?php foreach ($tiposViviendas as $tv): ?>
                    <option value="<?= (int)$tv['IdTVivienda'] ?>"><?= htmlspecialchars((string)$tv['NomTVivienda'], ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
        </section>
        <section>
            <label for="pais">País:</label>
            <?php renderSelectPaises($paises, '', '-- Todos los países --', 'pais', 'pais'); ?><br>
            <label for="ciudad">Ciudad: <input type="text" id="ciudad" name="ciudad"></label>
        </section>
        <section>
            <label for="precio_min">Precio entre: <input type="number" id="precio_min" name="precio_min" step="0.01" min="0"></label>
            <label for="precio_max">y: <input type="number" id="precio_max" name="precio_max" step="0.01" min="0"></label>
        </section>
        <section>
            <label for="fecha_min">Fecha entre: <input type="date" id="fecha_min" name="fecha_min"></label>
            <label for="fecha_max">y: <input type="date" id="fecha_max" name="fecha_max"></label>
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