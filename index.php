<?php

$ciudad = $_GET['ciudad'] ?? '';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Principal</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="style/barraNav.css">
    <link rel="stylesheet" href="style/index.css">
    <link rel="stylesheet" href="style/titulos.css">

    <script src="javascript/index.js"></script>
</head>

<body>

    <h3>URL: https://buscasa.gt.tc/index.php</h3>

    <nav>
        <ul>
            <li>
                <a href="busqueda.php" id="rgr">
                    <span><i class="fa-solid fa-magnifying-glass"></i> Búsqueda</span>
                </a>
            </li>

            <li>
                <a href="signUp.php" id="lgn">
                    <span>Registrarse</span>
                </a>
            </li>

            <li>
                <a href="partePrivada.php" id="rgr">
                    <span>Perfil</span>
                </a>
            </li>
        </ul>

        <nav>
            <ul>
                <li><span for="osc" id="lol">Modo oscuro:<input type="checkbox" id="osc"></span></li>
                <li><span for="letr" id="lol">Letra grande:<input type="checkbox" id="letr"></span></li>
                <li><span for="cont" id="lol">Contraste:<input type="checkbox" id="cont"></span></li>
                <li><span for="cont" id="lol">Ambos:<input type="checkbox" id="amb"></span></li>
            </ul>
        </nav>

        <section id="iniciar-sesion">

    <form action="controlAcc.php" method="POST" novalidate>

    <label for="loginUser">Usuario:</label>
    <input type="text" id="loginUser" name="loginUser">

    <label for="loginPass">Contraseña:</label>
    <input type="password" id="loginPass" name="loginPass">

    <button type="submit">Acceder</button>

</form>

<?php
if (isset($_GET["error"])) {

    if ($_GET["error"] === "vacio") {
        echo "<p>Debes introducir un usuario y una contraseña.</p>";
    }

    if ($_GET["error"] === "incorrecto") {
        echo "<p>El usuario o la contraseña son incorrectos.</p>";
    }
}
?>
</section>

        <br><br><br>

    </nav>


    <div>

        <!-- Buscador por ciudad -->

        <form action="resultadoBusqueda.php" method="GET">

            <input
                type="text"
                placeholder="Buscar por ciudad"
                id="buscador"
                name="ciudad"
            >

            <input type="submit" value="🔍">

        </form>

        <br>

        <input type="checkbox" id="print-toggle" style="display: none;">

        <label for="print-toggle" style="display: block; margin-bottom: 10px; color: black; cursor: pointer;">
            <u>Cambiar vista normal/impresión</u>
        </label>

        <br><br>

        <h2>Anuncios destacados</h2>

        <hr size="1px" align="left" color="black">

        <br><br>

        <section>
            <a href="anuncio.php">
                <img src="imagenes/casa1.jpeg">
            </a>
            <p>Casa en el Caribe</p>
        </section>

        <section>
            <a href="error.php">
                <img src="imagenes/apartamento1.jpeg">
            </a>
            <p>Apartamento en Murcia</p>
        </section>

    </div>


    <footer>

        <br><br>

        <hr size="1px" align="center" color="black">

        <p>&copy; 2026. Aviso de copyright: Contenido de uso libre.</p>

    </footer>

</body>
</html>
