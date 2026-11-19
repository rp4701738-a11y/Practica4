
<?php
require_once "permitidos.php";
require_once "conexion.php";

protegerRuta();

// =====================================================
// OBTENER ANUNCIOS DEL USUARIO
// =====================================================


function ReadData()
{
    $conn = OpenConnection();

    $usuario = $_SESSION["usuario"];

    $sql = "SELECT
                A.IdAnuncio,
                A.Titulo,
                A.FPrincipal AS Foto,
                A.Precio,
                A.Ciudad,
                A.Pais
            FROM Anuncios A
            INNER JOIN Usuarios U ON A.Usuario = U.IdUsuario
            WHERE U.NomUsuario = ?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error al preparar consulta: " . $conn->error);
    }

    $stmt->bind_param("s", $usuario);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        echo "<p>No tienes anuncios publicados.</p>";
    }

    while ($anuncio = $resultado->fetch_assoc()) {
        ?>
        <section>
            <a href="verAnuncio.php?id=<?= (int)$anuncio['IdAnuncio'] ?>">
                <img
                    src="<?= htmlspecialchars($anuncio['Foto'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    width="50"
                    height="50"
                    alt="<?= htmlspecialchars($anuncio['Titulo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </a>

            <h3><?= htmlspecialchars($anuncio['Titulo'] ?? '', ENT_QUOTES, 'UTF-8') ?></h3>

            <p>Ciudad: <?= htmlspecialchars($anuncio['Ciudad'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>

            <p>País (ID): <?= htmlspecialchars((string)($anuncio['Pais'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>

            <p>Precio: <?= htmlspecialchars((string)($anuncio['Precio'] ?? ''), ENT_QUOTES, 'UTF-8') ?> €</p>

            <a href="anadirFoto.php?anuncio=<?= (int)$anuncio['IdAnuncio'] ?>">
                Añadir foto
            </a>
        </section>
        <?php
    }

    $stmt->close();
    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis anuncios</title>

    <link rel="stylesheet" href="style/partePrivada.css">
    <link rel="stylesheet" href="style/barraNav.css">
</head>

<body>

    <nav>
        <ul>
            <li>
                <a href="index2.php" id="rgr">
                    <span>Menú principal</span>
                </a>
            </li>

            <li>
                <a href="partePrivada.php" id="rgr">
                    <span>Perfil (<?= htmlspecialchars(obtenerSaludo(), ENT_QUOTES, 'UTF-8') ?>)</span>
                </a>
            </li>

            <li>
                <a href="misAnuncios.php?salir=1" id="lgn">
                    <span>Salir</span>
                </a>
            </li>
        </ul>
    </nav>

    <?php mostrarBarraEstilos(); ?>

    <?php ReadData(); ?>

</body>
</html>
