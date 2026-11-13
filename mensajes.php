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
    <title>Mensajes</title>
    <link rel="stylesheet" href="style/barraNav.css">
    <link rel="stylesheet" href="style/mensajes.css">
    <link rel="stylesheet" href="style/titulos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <h3>URL: https://buscasa.gt.tc/mensajes.php</h3>
    <ul>
        <li><a href="index2.php" id="rgr"><span>Menú principal</span></a></li>
        <li><a href="busqueda.php" id="rgr"><span><i class="fa-solid fa-magnifying-glass"></i> Búsqueda</span></a></li>
        <li><a href="partePrivada.php" id="rgr"><span>Perfil (<?= obtenerSaludo() ?>)</span></a></li>
        <li><a href="mensajes.php?salir=1" id="lgn"><span>Salir</span></a></li>
    </ul>

    <?php mostrarBarraEstilos(); ?>

    <input type="checkbox" id="print-toggle" style="display: none;">
    <label for="print-toggle" style="display: block; margin-bottom: 10px; color: black; cursor: pointer; clear: both;">
        <u>Cambiar vista normal/impresión</u>
    </label>

    <h2>Mensajes</h2>
    <div>
        <p>
            Tipo: Solicitar una cita <br>
            Fecha: 25/03/26 <br>
            Usuario: Emily Lorial
        </p>
        <p class="negro">
            Mensaje: Buenos días, ¿podría ir el 27 de mayo sobre las 13:00 para ver un poco la casa?<br>
            Si no es posible me podría especificar otra fecha.<br>
        </p>
    </div>
    <div>
        <p>
            Tipo: Solicitar una cita <br>
            Fecha: 25/03/26 <br>
            Usuario: Emily Lorial
        </p>
        <p class="negro">
            Mensaje: Buenos días, ¿podría ir el 27 de mayo sobre las 13:00 para ver un poco la casa?<br>
            Si no es posible me podría especificar otra fecha.<br>
        </p>
    </div>
    <div>
        <p>
            Tipo: Solicitar una cita <br>
            Fecha: 25/03/26 <br>
            Usuario: Emily Lorial
        </p>
        <p class="negro">
            Mensaje: Buenos días, ¿podría ir el 27 de mayo sobre las 13:00 para ver un poco la casa?<br>
            Si no es posible me podría especificar otra fecha.<br>
        </p>
    </div>

    <footer>
        <br><br>
        <hr size="1px" align="center" color="black">
        <p>&copy; 2026. Aviso de copyright: Contenido de uso libre.</p>
    </footer>
</body>
</html>
