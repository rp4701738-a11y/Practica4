
<?php

// Recoger los datos del formulario

$resumen = $_POST['textarea1'] ?? '';

$nombre = $_POST['nombre'] ?? '';
$email = $_POST['email'] ?? '';
$telefono = $_POST['telefono'] ?? '';

$calle = $_POST['calle'] ?? '';
$numero = $_POST['numero'] ?? '';
$piso = $_POST['piso'] ?? '';
$puerta = $_POST['puerta'] ?? '';
$codigo_postal = $_POST['codigo_postal'] ?? '';
$localidad = $_POST['localidad'] ?? '';
$provincia = $_POST['provincia'] ?? '';
$pais = $_POST['pais'] ?? '';

$color_portada = $_POST['color_portada'] ?? '#000000';
$copias = (int)($_POST['copias'] ?? 1);
$resolucion = (int)($_POST['resolucion'] ?? 150);

$anuncio = $_POST['anuncio'] ?? '';
$fecha = $_POST['fecha'] ?? '';

$color = $_POST['color'] ?? '';
$precio_anuncio = isset($_POST['precio_anuncio'])
    ? 'Sí'
    : 'No';

// Calcular el precio

$precio_fotos = 0.04 * 3;
$precio_color = ($color == 'A color') ? 0.20 : 0.10;
$precio_paginas = 0.20;

// Precio según resolución
$precio_resolucion = ($resolucion / 150) * 0.02;

// Precio por copia
$precio_copia = $precio_fotos
    + $precio_color
    + $precio_paginas
    + $precio_resolucion;

// Precio de todas las copias
$precio_total = $precio_copia * $copias;

$precio_total = round($precio_total, 2);

// Función para evitar introducir HTML malicioso
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
        <li><a href="index2.php" id="rgr">
            <span>Menú principal</span>
        </a></li>

        <li><a href="busqueda.php" id="rgr">
            <span>Búsqueda</span>
        </a></li>

        <li><a href="partePrivada.php" id="rgr">
            <span>Perfil</span>
        </a></li>
    </ul>
</nav>

<nav>
    <ul>
        <li>Modo oscuro:
            <input type="checkbox" id="osc">
        </li>

        <li>Letra grande:
            <input type="checkbox" id="letr">
        </li>

        <li>Contraste:
            <input type="checkbox" id="cont">
        </li>

        <li>Ambos:
            <input type="checkbox" id="amb">
        </li>
    </ul>
</nav>

<input type="checkbox" id="print-toggle" style="display:none;">

<label for="print-toggle">
    <u>Cambiar vista normal/impresión</u>
</label>

<h3>Resumen del folleto:</h3>

<section>

    <p>
        ----Resumen-----------
        <br>
        <?= nl2br(limpiar($resumen)) ?>
    </p>

    <br>

    <p>
        ----Contacto------------
        <br>
        Nombre: <?= limpiar($nombre) ?>
        <br>
        Email: <?= limpiar($email) ?>
        <br>
        Número: <?= limpiar($telefono) ?>
    </p>

    <br>

    <p>
        ----Dirección del anuncio---
        <br>
        Calle: <?= limpiar($calle) ?>
        <br>
        Número: <?= limpiar($numero) ?>
        <br>
        Piso: <?= limpiar($piso) ?>
        <br>
        Puerta: <?= limpiar($puerta) ?>
        <br>
        Código postal: <?= limpiar($codigo_postal) ?>
        <br>
        Localidad: <?= limpiar($localidad) ?>
        <br>
        Provincia: <?= limpiar($provincia) ?>
        <br>
        País: <?= limpiar($pais) ?>
    </p>

    <br>

    <p>
        ----Datos del folleto---
        <br>
        Anuncio: <?= limpiar($anuncio) ?>
        <br>
        Color de fondo: <?= limpiar($color_portada) ?>
        <br>
        Número de copias: <?= $copias ?>
        <br>
        Resolución: <?= $resolucion ?> DPI
        <br>
        Fecha: <?= limpiar($fecha) ?>
        <br>
        Color: <?= limpiar($color) ?>
        <br>
        Añadir precio al anuncio: <?= $precio_anuncio ?>
    </p>

    <br>

    <p>
        Precio total:
        <?= number_format($precio_total, 2, ',', '.') ?> €
    </p>

</section>

<footer>
    <br><br>
    <hr size="1px" align="center" color="black">

    <p>
        &copy; 2026. Aviso de copyright:
        Contenido de uso libre.
    </p>
</footer>

</body>
</html>
