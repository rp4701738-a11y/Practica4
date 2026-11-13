<?php
require_once "permitidos.php";
protegerRuta();

if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_POST["actualizar_estilos"])) {
    $_SESSION["datos_folleto"] = $_POST;
}
$datosFolleto = $_SESSION["datos_folleto"] ?? $_POST;
session_write_close();

$resumen = $datosFolleto['textarea1'] ?? '';
$nombre = $datosFolleto['nombre'] ?? '';
$email = $datosFolleto['email'] ?? '';
$telefono = $datosFolleto['telefono'] ?? '';
$calle = $datosFolleto['calle'] ?? '';
$numero = $datosFolleto['numero'] ?? '';
$piso = $datosFolleto['piso'] ?? '';
$puerta = $datosFolleto['puerta'] ?? '';
$codigo_postal = $datosFolleto['codigo_postal'] ?? '';
$localidad = $datosFolleto['localidad'] ?? '';
$provincia = $datosFolleto['provincia'] ?? '';
$pais = $datosFolleto['pais'] ?? '';
$color_portada = $datosFolleto['color_portada'] ?? '#000000';
$copias = (int)($datosFolleto['copias'] ?? 1);
$resolucion = (int)($datosFolleto['resolucion'] ?? 150);
$anuncio = $datosFolleto['anuncio'] ?? '';
$fecha = $datosFolleto['fecha'] ?? '';
$color = $datosFolleto['color'] ?? '';
$precio_anuncio = isset($datosFolleto['precio_anuncio']) ? 'Sí' : 'No';

$precio_fotos = 0.04 * 3;
$precio_color = ($color == 'A color') ? 0.20 : 0.10;
$precio_paginas = 0.20;
$precio_resolucion = ($resolucion / 150) * 0.02;
$precio_copia = $precio_fotos + $precio_color + $precio_paginas + $precio_resolucion;
$precio_total = round($precio_copia * $copias, 2);

function limpiar($dato) {
    return htmlspecialchars($dato, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respuesta</title>
    <link rel="stylesheet" href="style/barraNav.css">
    <link rel="stylesheet" href="style/titulos.css">
    <link rel="stylesheet" href="style/respuestaFolleto.css">
</head>
<body>
    <h3>URL: https://buscasa.gt.tc/respuestaFolleto.php</h3>
    <nav>
        <ul>
            <li><a href="index2.php" id="rgr"><span>Menú principal</span></a></li>
            <li><a href="busqueda.php" id="rgr"><span>Búsqueda</span></a></li>
            <li><a href="partePrivada.php" id="rgr"><span>Perfil (<?= obtenerSaludo() ?>)</span></a></li>
            <li><a href="respuestaFolleto.php?salir=1" id="lgn"><span>Salir</span></a></li>
        </ul>
    </nav>

    <?php mostrarBarraEstilos(); ?>

    <input type="checkbox" id="print-toggle" style="display:none;">
    <label for="print-toggle">
        <u>Cambiar vista normal/impresión</u>
    </label>

    <h3>Resumen del folleto:</h3>
    <section>
        <p>
            ----Resumen-----------<br>
            <?= nl2br(limpiar($resumen)) ?>
        </p><br>
        <p>
            ----Contacto------------<br>
            Nombre: <?= limpiar($nombre) ?><br>
            Email: <?= limpiar($email) ?><br>
            Número: <?= limpiar($telefono) ?>
        </p><br>
        <p>
            ----Dirección del anuncio---<br>
            Calle: <?= limpiar($calle) ?><br>
            Número: <?= limpiar($numero) ?><br>
            Piso: <?= limpiar($piso) ?><br>
            Puerta: <?= limpiar($puerta) ?><br>
            Código postal: <?= limpiar($codigo_postal) ?><br>
            Localidad: <?= limpiar($localidad) ?><br>
            Provincia: <?= limpiar($provincia) ?><br>
            País: <?= limpiar($pais) ?>
        </p><br>
        <p>
            ----Datos del folleto---<br>
            Anuncio: <?= limpiar($anuncio) ?><br>
            Color de fondo: <?= limpiar($color_portada) ?><br>
            Número de copias: <?= $copias ?><br>
            Resolución: <?= $resolucion ?> DPI<br>
            Fecha: <?= limpiar($fecha) ?><br>
            Color: <?= limpiar($color) ?><br>
            Añadir precio al anuncio: <?= $precio_anuncio ?>
        </p><br>
        <p>
            Precio total: <?= number_format($precio_total, 2, ',', '.') ?> €
        </p>
    </section>

    <footer>
        <br><br>
        <hr size="1px" align="center" color="black">
        <p>&copy; 2026. Aviso de copyright: Contenido de uso libre.</p>
    </footer>
</body>
</html>
