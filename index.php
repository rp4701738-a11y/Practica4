<?php
require_once "permitidos.php";
$errorFlash = obtenerFlash("error");
session_write_close();
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
            <?php if (!isset($_SESSION["usuario"])): ?>
                <li>
                    <a href="signIn.php" id="lgn">
                        <span>Iniciar sesión</span>
                    </a>
                </li>
                <li>
                    <a href="signUp.php" id="lgn">
                        <span>Registrarse</span>
                    </a>
                </li>
            <?php else: ?>
                <li>
                    <a href="partePrivada.php" id="rgr">
                        <span>Perfil (<?= obtenerSaludo() ?>)</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?salir=1" id="lgn">
                        <span>Salir</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>

        <?php mostrarBarraEstilos(); ?>

        <section id="iniciar-sesion">
            <?php if (isset($_SESSION["usuario"])): ?>
                <p>
                    Hola <strong><?= htmlspecialchars($_SESSION["nombre"] ?? $_SESSION["usuario"]) ?></strong>,
                    su última visita fue el <strong><?= htmlspecialchars($_SESSION["ultima_visita"] ?? date("d/m/y \a \l\a\s H:i")) ?></strong>
                </p>
                <form action="partePrivada.php" method="GET" style="display:inline;">
                    <button type="submit">Acceder</button>
                </form>
                <form action="index.php" method="POST" style="display:inline;">
                    <button type="submit" name="salir">Salir</button>
                </form>
            <?php else: ?>
                <form action="controlAcc.php" method="POST" novalidate>
                    <label for="loginUser">Usuario:</label>
                    <input type="text" id="loginUser" name="loginUser"><br>

                    <label for="loginPass">Contraseña:</label>
                    <input type="password" id="loginPass" name="loginPass"><br>

                    <label for="recordarme">Recordarme en este equipo:</label>
                    <input type="checkbox" id="recordarme" name="recordarme" value="1"><br>

                    <button type="submit">Acceder</button>
                </form>
            <?php endif; ?>

            <?php if ($errorFlash !== null): ?>
                <p style="color: red; font-weight: bold;"><?= htmlspecialchars($errorFlash) ?></p>
            <?php endif; ?>
        </section>
        <br><br><br>
    </nav>

    <div>
        <form action="resultadoBusqueda.php" method="GET">
            <input type="text" placeholder="Buscar por ciudad" id="buscador" name="ciudad">
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
        <?php foreach ($catalogoAnuncios as $anuncio): ?>
            <section style="display: inline-block; vertical-align: top;">
                <a href="anuncio.php?id=<?= (int)$anuncio['id'] ?>">
                    <img src="<?= htmlspecialchars($anuncio['imagen']) ?>" alt="<?= htmlspecialchars($anuncio['titulo']) ?>">
                </a>
                <p><?= htmlspecialchars($anuncio['titulo']) ?></p>
            </section>
        <?php endforeach; ?>

        <?php mostrarPanelUltimosAnuncios($catalogoAnuncios); ?>
    </div>

    <footer>
        <br><br>
        <hr size="1px" align="center" color="black">
        <p>© 2026. Aviso de copyright: Contenido de uso libre.</p>
    </footer>
</body>
</html>