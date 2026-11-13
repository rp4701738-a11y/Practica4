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
    <title>Perfil</title>
    <link rel="stylesheet" href="style/partePrivada.css">
    <link rel="stylesheet" href="style/barraNav.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <h3>URL: https://buscasa.gt.tc/partePrivada.php</h3>
    <nav>
        <ul>
            <li>
                <a href="index2.php" id="rgr">
                    <span>Menú principal</span>
                </a>
            </li>
            <li>
                <a href="busqueda.php" id="rgr">
                    <span><i class="fa-solid fa-magnifying-glass"></i> Búsqueda</span>
                </a>
            </li>
            <li>
                <a href="partePrivada.php?salir=1" id="lgn">
                    <span>Salir</span>
                </a>
            </li>
        </ul>
    </nav>

    <?php mostrarBarraEstilos(); ?>

    <input type="checkbox" id="print-toggle" style="display:none;">
    <label for="print-toggle" style="display:block; margin-bottom:10px; color:black; cursor:pointer; clear:both;">
        <u>Cambiar vista normal/impresión</u>
    </label>

    <h2><?= obtenerSaludo() ?>.</h2>

    <?php if (!empty($_SESSION["ultima_visita"])): ?>
        <p>Hola <strong><?= htmlspecialchars($_SESSION["nombre"] ?? $_SESSION["usuario"]) ?></strong>, su última visita fue el <strong><?= htmlspecialchars($_SESSION["ultima_visita"]) ?></strong></p>
    <?php endif; ?>

    <form method="POST" action="partePrivada.php">
        <button type="submit" name="salir">Salir</button>
    </form>

    <ul class="boton">
        <li><a href="crearAnuncio.php"><span>Crear anuncio</span></a></li>
        <li><a href="misAnuncios.php"><span>Mis anuncios</span></a></li>
        <li><a href="pedirfolleto.php"><span>Diseñar folleto publicitario</span></a></li>
        <li><a href="mensajes.php"><span>Mis mensajes</span></a></li>
        <li><a href="partePrivada.php?salir=1"><span>Salir (Cerrar sesión)</span></a></li>
    </ul>

    <footer>
        <br><br>
        <hr size="1px" align="center" color="black">
        <p>© 2026. Aviso de copyright: Contenido de uso libre.</p>
    </footer>
</body>
</html>