
<?php
require_once "permitidos.php";
require_once "conexion.php";

protegerRuta();

// =====================================================
// LEER Y MOSTRAR LOS DATOS DEL ANUNCIO
// =====================================================

function ReadData()
{
    // Recibir el identificador del anuncio
    $id = $_GET["id"] ?? $_GET["Id"] ?? null;

    // Validar el identificador
    if (
        $id === null ||
        filter_var($id, FILTER_VALIDATE_INT) === false ||
        (int)$id <= 0
    ) {
        echo "<p>ID de anuncio no válido.</p>";
        return;
    }

    $id = (int)$id;

    $conn = OpenConnection();

    // Consultar los datos del anuncio
    $sql = "SELECT
                A.IdAnuncio,
                A.Titulo,
                A.FPrincipal,
                A.Alternativo,
                TV.NomTVivienda AS TipoVivienda,
                TA.NomTAnuncio AS TipoAnuncio,
                A.Texto,
                A.Precio,
                A.Ciudad,
                P.NomPais AS Pais,
                A.Superficie,
                A.NHabitaciones,
                A.NBanyos,
                A.Planta,
                A.Anyo,
                A.FRegistro,
                U.IdUsuario AS IdPropietario,
                U.NomUsuario AS Usuario
            FROM Anuncios A
            INNER JOIN TiposViviendas TV
                ON A.TVivienda = TV.IdTVivienda
            INNER JOIN TiposAnuncios TA
                ON A.TAnuncio = TA.IdTAnuncio
            LEFT JOIN Paises P
                ON A.Pais = P.IdPais
            INNER JOIN Usuarios U
                ON A.Usuario = U.IdUsuario
            WHERE A.IdAnuncio = ?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo "<p>Error al preparar la consulta.</p>";
        $conn->close();
        return;
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        echo "<p>No se ha encontrado el anuncio.</p>";
    } else {

        $row = $resultado->fetch_assoc();

        ?>

        <section>

            <h2>
                <?= htmlspecialchars($row["Titulo"] ?? "", ENT_QUOTES, "UTF-8") ?>
            </h2>

            <p><strong>Foto principal:</strong></p>

            <?php if (!empty($row["FPrincipal"])): ?>
                <a href="verFotos.php?id=<?= $id ?>">
                    <img
                        src="<?= htmlspecialchars($row["FPrincipal"], ENT_QUOTES, "UTF-8") ?>"
                        width="250"
                        
                    >
                </a>
            <?php endif; ?>

            <p>
                <strong>Tipo de vivienda:</strong>
                <?= htmlspecialchars($row["TipoVivienda"] ?? "", ENT_QUOTES, "UTF-8") ?>
            </p>

            <p>
                <strong>Tipo de anuncio:</strong>
                <?= htmlspecialchars($row["TipoAnuncio"] ?? "", ENT_QUOTES, "UTF-8") ?>
            </p>

            <p>
                <strong>Descripción:</strong>
                <?= nl2br(htmlspecialchars($row["Texto"] ?? "", ENT_QUOTES, "UTF-8")) ?>
            </p>

            <p>
                <strong>Texto alternativo:</strong>
                <?= htmlspecialchars($row["Alternativo"] ?? "", ENT_QUOTES, "UTF-8") ?>
            </p>

            <p>
                <strong>Precio:</strong>
                <?= htmlspecialchars((string)($row["Precio"] ?? ""), ENT_QUOTES, "UTF-8") ?> €
            </p>

            <p>
                <strong>Ciudad:</strong>
                <?= htmlspecialchars($row["Ciudad"] ?? "", ENT_QUOTES, "UTF-8") ?>
            </p>

            <p>
                <strong>País:</strong>
                <?= htmlspecialchars($row["Pais"] ?? "", ENT_QUOTES, "UTF-8") ?>
            </p>

            <p>
                <strong>Superficie:</strong>
                <?= htmlspecialchars((string)($row["Superficie"] ?? ""), ENT_QUOTES, "UTF-8") ?> m²
            </p>

            <p>
                <strong>Número de habitaciones:</strong>
                <?= htmlspecialchars((string)($row["NHabitaciones"] ?? ""), ENT_QUOTES, "UTF-8") ?>
            </p>

            <p>
                <strong>Número de baños:</strong>
                <?= htmlspecialchars((string)($row["NBanyos"] ?? ""), ENT_QUOTES, "UTF-8") ?>
            </p>

            <p>
                <strong>Planta:</strong>
                <?= htmlspecialchars((string)($row["Planta"] ?? ""), ENT_QUOTES, "UTF-8") ?>
            </p>

            <p>
                <strong>Año de construcción:</strong>
                <?= htmlspecialchars((string)($row["Anyo"] ?? ""), ENT_QUOTES, "UTF-8") ?>
            </p>

            <p>
                <strong>Fecha de registro:</strong>
                <?= htmlspecialchars($row["FRegistro"] ?? "", ENT_QUOTES, "UTF-8") ?>
            </p>

            <p>
                <strong>Usuario:</strong>
                 <a href="perfil.php?id=<?= (int)$row["IdPropietario"] ?>">
                <?= htmlspecialchars(
                    $row["Usuario"] ?? "",
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>
                </a>
            </p>

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
    <title>Detalles del anuncio</title>
</head>

<body>

    <h1>Detalles del anuncio</h1>

    <?php ReadData(); ?>

    <p>
        <a href="busqueda.php">Volver a la búsqueda</a>
    </p>

</body>
</html>
