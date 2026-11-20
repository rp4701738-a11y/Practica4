<?php
require_once "permitidos.php";
require_once "conexion.php";

protegerRuta();

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
                P.NomPais AS Pais
            FROM Anuncios A
            INNER JOIN Usuarios U
                ON A.Usuario = U.IdUsuario
            LEFT JOIN Paises P
                ON A.Pais = P.IdPais
            WHERE U.NomUsuario = ?
            ORDER BY A.FRegistro DESC";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error al preparar consulta: " . $conn->error);
    }

    $stmt->bind_param("s", $usuario);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $totalAnuncios = $resultado->num_rows;

    echo "<p><strong>Total de anuncios:</strong> " . (int)$totalAnuncios . "</p>";

    if ($totalAnuncios === 0) {
        echo "<p>No tienes anuncios publicados.</p>";
    }

    while ($anuncio = $resultado->fetch_assoc()) {
        ?>

        <section>

            <h2>
                <?= htmlspecialchars($anuncio['Titulo'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </h2>

            <a href="verFotos.php?id=<?= (int)$anuncio['IdAnuncio'] ?>">

                <?php if (!empty($anuncio['Foto'])): ?>

                    <img
                        src="<?= htmlspecialchars($anuncio['Foto'], ENT_QUOTES, 'UTF-8') ?>"
                        width="150"
                        height="120"
                        alt="Fotografía principal del anuncio">

                <?php else: ?>

                    <p>Sin fotografía principal</p>

                <?php endif; ?>

            </a>

            <p>
                Ciudad:
                <?= htmlspecialchars($anuncio['Ciudad'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </p>

            <p>
                País:
                <?= htmlspecialchars($anuncio['Pais'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </p>

            <p>
                Precio:
                <?= htmlspecialchars((string)($anuncio['Precio'] ?? ''), ENT_QUOTES, 'UTF-8') ?> €
            </p>

            <p>
                <a href="verAnuncio.php?id=<?= (int)$anuncio['IdAnuncio'] ?>">
                    <button type="button">Ver anuncio</button>
                </a>
            </p>

            <p>
                <a href="verFotos.php?id=<?= (int)$anuncio['IdAnuncio'] ?>">
                    Ver todas las fotografías del anuncio
                </a>
            </p>

            <p>
                <a href="anadirFoto.php?anuncio=<?= (int)$anuncio['IdAnuncio'] ?>">
                    Añadir fotografía
                </a>
            </p>

        </section>

        <hr>

        <?php
    }

    $resultado->close();
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

    <h1>Mis anuncios publicados</h1>

    <?php ReadData(); ?>

</body>
</html>