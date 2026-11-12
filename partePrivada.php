<?php
session_start();
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
                    <span>
                        <i class="fa-solid fa-magnifying-glass"></i>
                        Búsqueda
                    </span>
                </a>
            </li>
        </ul>

        <ul>
            <li>
                <span id="lol">Modo oscuro:
                    <input type="checkbox" id="osc">
                </span>
            </li>

            <li>
                <span id="lol">Letra grande:
                    <input type="checkbox" id="letr">
                </span>
            </li>

            <li>
                <span id="lol">Contraste:
                    <input type="checkbox" id="cont">
                </span>
            </li>

            <li>
                <span id="lol">Ambos:
                    <input type="checkbox" id="amb">
                </span>
            </li>
        </ul>
    </nav>

    <input type="checkbox" id="print-toggle" style="display:none;">

    <label for="print-toggle"
           style="display:block; margin-bottom:10px; color:black; cursor:pointer; clear:both;">
        <u>Cambiar vista normal/impresión</u>
    </label>

    <?php
    date_default_timezone_set('Europe/Madrid');

    $hora = (int) date('H');
    $usuario = $_SESSION['usuario'] ?? "Usuario";
    if ($hora >= 6 && $hora < 12) {
        echo "Buenos días, " . $usuario;
    } elseif ($hora >= 12 && $hora < 16) {
        echo "Hola, " . $usuario;
    }elseif ($hora >= 16 && $hora < 20) {
        echo "Buenas tardes, ";
    } else {
        echo "Buenas noches, ";
    }
    ?>

    <ul class="boton">

        <li><a href=""><span>Mis datos</span></a></li>

        <li><a href=""><span>Darme de baja</span></a></li>

        <li><a href="misAnuncios.php"><span>Mis anuncios</span></a></li>

        <li><a href="pedirFolleto.php"><span>Diseñar folleto publicitario</span></a></li>

        <li><a href="mensajes.php"><span>Mis mensajes</span></a></li>

    </ul>

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
