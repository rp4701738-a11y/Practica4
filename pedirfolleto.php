<?php
require_once "permitidos.php";
protegerRuta();
session_write_close();

function cal($m, $i) {
    $n = 2; $q = 1.8; $p = 1.6;
    if ($i == 0) return $m;
    if ($i < 4) $m += $n;
    elseif ($i < 10) $m += $q;
    else $m += $p;
    return round($m, 2);
}
function cal1($m, $i) {
    $n = 2.6; $q = 2.4; $p = 2.2;
    if ($i == 0) return $m;
    if ($i < 4) $m += $n;
    elseif ($i < 10) $m += $q;
    else $m += $p;
    return round($m, 2);
}
function cal2($m, $i) {
    $n = 3.5; $q = 3.3; $p = 3.1;
    if ($i == 0) return $m;
    if ($i < 4) $m += $n;
    elseif ($i < 10) $m += $q;
    else $m += $p;
    return round($m, 2);
}
function cal3($m, $i) {
    $n = 4.1; $q = 3.9; $p = 3.7;
    if ($i == 0) return $m;
    if ($i < 4) $m += $n;
    elseif ($i < 10) $m += $q;
    else $m += $p;
    return round($m, 2);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Folleto</title>
    <link rel="stylesheet" href="style/barraNav.css">
    <link rel="stylesheet" href="style/titulos.css">
    <link rel="stylesheet" href="style/pedirFolleto.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <h3>URL: https://buscasa.gt.tc/pedirfolleto.php</h3>
    <nav>
        <ul>
            <li><a href="index2.php" id="rgr"><span>Menú principal</span></a></li>
            <li><a href="busqueda.php" id="rgr"><span><i class="fa-solid fa-magnifying-glass"></i> Búsqueda</span></a></li>
            <li><a href="partePrivada.php" id="rgr"><span>Perfil (<?= obtenerSaludo() ?>)</span></a></li>
            <li><a href="pedirfolleto.php?salir=1" id="lgn"><span>Salir</span></a></li>
        </ul>
    </nav>

    <?php mostrarBarraEstilos(); ?>

    <input type="checkbox" id="print-toggle" style="display: none;">
    <label for="print-toggle" style="display: block; margin-bottom: 10px; color: black; cursor: pointer; clear: both;">
        <u>Cambiar vista normal/impresión</u>
    </label>

    <h2>Diseña tu folleto</h2>
    <hr size="1px" align="left" color="black">

    <button id="tr" type="button" onclick="mostrar()">Ver costes</button>
    <section id="tb">
        <br>
        <table id="ta" border="1">
            <tr>
                <td></td>
                <td></td>
                <td colspan="2">Blanco y negro</td>
                <td colspan="2">Color</td>
            </tr>
            <tr>
                <td>Nº de <br> páginas</td>
                <td>Nº de <br> fotos</td>
                <td>150-300 dpi</td>
                <td>450-900 dpi</td>
                <td>150-300 dpi</td>
                <td>450-900 dpi</td>
            </tr>
            <?php
            $m = 12; $n = 12.6; $j = 13.5; $p = 14.1;
            for ($i = 0; $i < 15; $i++) {
                echo "<tr>";
                echo "<td>" . ($i + 1) . "</td>";
                echo "<td>" . (($i + 1) * 3) . "</td>";
                echo "<td>" . cal($m, $i) . " €</td>";
                echo "<td>" . cal1($n, $i) . " €</td>";
                echo "<td>" . cal2($j, $i) . " €</td>";
                echo "<td>" . cal3($p, $i) . " €</td>";
                echo "</tr>";
                $m = cal($m, $i);
                $n = cal1($n, $i);
                $j = cal2($j, $i);
                $p = cal3($p, $i);
            }
            ?>
        </table>
    </section>

    <form action="respuestaFolleto.php" method="POST">
        <br>
        <p>Esta página sirve para diseñar un folleto publicitario de tu anuncio, el cual podrás imprimir y colgar por ahí.</p>
        <label>Tarifas:</label>
        <table>
            <tr><th>Tipo</th><th>Precio</th></tr>
            <tr><td>Cantidad de fotos</td><td>4 céntimos por foto</td></tr>
            <tr><td>A color</td><td>20 céntimos</td></tr>
            <tr><td>En blanco y negro</td><td>10 céntimos</td></tr>
            <tr><td>Número de páginas</td><td>20 céntimos por página</td></tr>
            <tr><td>Resolución</td><td>2 céntimos por cada pulgada</td></tr>
        </table>

        <br><label>Resumen del anuncio: <br><textarea name="textarea1" rows="4" cols="50" maxlength="4000"></textarea></label>
        <br><br><label>Tus datos:</label><br>
        <label>Nombre y apellido: <input type="text" name="nombre" maxlength="200" required></label><br>
        <label>Email: <input type="email" name="email" maxlength="200" required></label><br>
        <label>Teléfono de contacto: <input type="tel" name="telefono"></label><br><br>

        <label>Dirección del anuncio:</label><br>
        <label>Calle: <input type="text" name="calle" required></label><br>
        <label>Número: <input type="text" name="numero" required></label><br>
        <label>Piso: <input type="text" name="piso" required></label><br>
        <label>Puerta: <input type="text" name="puerta" required></label><br>
        <label>Código postal: <input type="text" name="codigo_postal" required></label><br>
        <label>Localidad: <input type="text" name="localidad" required></label><br>
        <label>Provincia: <input type="text" name="provincia" required></label><br>
        <label>País: <input type="text" name="pais" required></label><br><br>

        <label>Color de la portada: <input type="color" name="color_portada" value="#000000" required></label><br>
        <label>Número de copias: <input type="number" name="copias" value="1" min="1" max="99" required></label><br>
        <label>Resolución de las fotos: <input type="number" name="resolucion" step="150" value="150" min="150" max="900" required> DPI</label><br><br>

        <label for="anuncio">Anuncio:</label>
        <input list="anuncios" name="anuncio" id="anuncio" required>
        <datalist id="anuncios">
            <option value="Piso en Alicante">
            <option value="Casa rural de Crevillente">
            <option value="Apartamento en Elda">
        </datalist><br>

        <label>¿Cuándo quiere recibir el envío postal?: <input type="date" name="fecha"></label><br>
        <label>¿Impresión a color o en blanco y negro?</label><br>
        <input type="radio" id="blanco-negro" name="color" value="Blanco y negro" required>
        <label for="blanco-negro">Blanco y negro</label><br>
        <input type="radio" id="a-color" name="color" value="A color">
        <label for="a-color">A color</label><br>

        <label>¿Añadir el precio en el anuncio?: <input type="checkbox" name="precio_anuncio" value="Sí"></label><br><br>
        <input type="submit" value="Enviar">
    </form>

    <footer>
        <br><br>
        <hr size="1px" align="center" color="black">
        <p>© 2026. Aviso de copyright: Contenido de uso libre.</p>
    </footer>
    <script src="javascript/pedirFolleto.js"></script>
</body>
</html>