
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
    } else {
        echo "<p>No se ha encontrado el usuario.</p>";
    }

    mysqli_stmt_close($stmt);
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
