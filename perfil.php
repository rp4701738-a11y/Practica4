<?php
require_once "conexion.php";

function ReadData()
{
    $Id = $_GET["id"] ?? null;

    if (
        $Id === null ||
        filter_var($Id, FILTER_VALIDATE_INT) === false ||
        (int)$Id <= 0
    ) {
        echo "<p>ID de usuario no válido.</p>";
        return;
    }

    $Id = (int)$Id;
    $conn = OpenConnection();

    $sql = "SELECT
                IdUsuario,
                NomUsuario,
                Email,
                FRegistro,
                Foto
            FROM Usuarios
            WHERE IdUsuario = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        die("Error en la consulta: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "i", $Id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        ?>

        <section>

            <h2>Perfil del propietario</h2>

            <p>
                <strong>Nombre de usuario:</strong>
                <?= htmlspecialchars($row["NomUsuario"], ENT_QUOTES, "UTF-8") ?>
            </p>

            <p>
                <strong>Email:</strong>
                <?= htmlspecialchars($row["Email"], ENT_QUOTES, "UTF-8") ?>
            </p>

            <p>
                <strong>Fecha de registro:</strong>
                <?= !empty($row["FRegistro"])
                    ? date("d/m/Y H:i", strtotime($row["FRegistro"]))
                    : "" ?>
            </p>

            <p><strong>Fotografía del usuario:</strong></p>

            <?php if (!empty($row["Foto"])): ?>
                <img
                    src="<?= htmlspecialchars($row["Foto"], ENT_QUOTES, "UTF-8") ?>"
                    width="200"
                    alt="Foto del usuario"
                >
            <?php else: ?>
                <p>El usuario no tiene fotografía.</p>
            <?php endif; ?>

        </section>

        <?php
        mysqli_free_result($result);
        mysqli_stmt_close($stmt);

        $sqlAnuncios = "SELECT
                            IdAnuncio,
                            Titulo,
                            FPrincipal,
                            Alternativo,
                            Ciudad,
                            Precio
                        FROM Anuncios
                        WHERE Usuario = ?
                        ORDER BY FRegistro DESC";

        $stmtAnuncios = mysqli_prepare($conn, $sqlAnuncios);

        if (!$stmtAnuncios) {
            echo "<p>Error al consultar los anuncios del usuario.</p>";
        } else {
            mysqli_stmt_bind_param($stmtAnuncios, "i", $Id);
            mysqli_stmt_execute($stmtAnuncios);
            $resultAnuncios = mysqli_stmt_get_result($stmtAnuncios);
            ?>

            <section>

                <h2>Anuncios del usuario (<?= mysqli_num_rows($resultAnuncios) ?>)</h2>

                <?php if (mysqli_num_rows($resultAnuncios) === 0): ?>
                    <p>Este usuario no tiene anuncios publicados.</p>
                <?php else: ?>
                    <?php while ($anuncio = mysqli_fetch_assoc($resultAnuncios)): ?>
                        <article>
                            <h3>
                                <a href="anuncio.php?id=<?= (int)$anuncio["IdAnuncio"] ?>">
                                    <?= htmlspecialchars($anuncio["Titulo"] ?? "", ENT_QUOTES, "UTF-8") ?>
                                </a>
                            </h3>

                            <?php if (!empty($anuncio["FPrincipal"])): ?>
                                <a href="anuncio.php?id=<?= (int)$anuncio["IdAnuncio"] ?>">
                                    <img
                                        src="<?= htmlspecialchars($anuncio["FPrincipal"], ENT_QUOTES, "UTF-8") ?>"
                                        width="200"
                                        alt="<?= htmlspecialchars($anuncio["Alternativo"] ?? $anuncio["Titulo"] ?? "", ENT_QUOTES, "UTF-8") ?>"
                                    >
                                </a>
                            <?php endif; ?>

                            <p>
                                <strong>Ciudad:</strong>
                                <?= htmlspecialchars($anuncio["Ciudad"] ?? "", ENT_QUOTES, "UTF-8") ?>
                            </p>

                            <p>
                                <strong>Precio:</strong>
                                <?= htmlspecialchars(number_format((float)($anuncio["Precio"] ?? 0), 2, ",", "."), ENT_QUOTES, "UTF-8") ?> €
                            </p>
                        </article>
                        <hr>
                    <?php endwhile; ?>
                <?php endif; ?>

            </section>

            <?php
            mysqli_free_result($resultAnuncios);
            mysqli_stmt_close($stmtAnuncios);
        }
    } else {
        echo "<p>No se ha encontrado el usuario.</p>";
        mysqli_free_result($result);
        mysqli_stmt_close($stmt);
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del propietario</title>
</head>

<body>

    <?php ReadData(); ?>

    <p>
        <a href="javascript:history.back()">Volver al anuncio</a>
    </p>

</body>
</html>