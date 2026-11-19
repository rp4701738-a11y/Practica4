
<?php
require_once "permitidos.php";
require_once "conexion.php";

protegerRuta();

function ReadData()
{
    // Obtener y validar el ID del anuncio
    $id = $_GET["id"] ?? $_GET["Id"] ?? null;

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
    $sql = "SELECT
                A.IdAnuncio,
                A.Titulo,
                A.FPrincipal,
                A.Alternativo,
                A.Precio,
                A.Ciudad,
                P.NomPais AS Pais,
                TV.NomTVivienda AS TipoVivienda,
                TA.NomTAnuncio AS TipoAnuncio,
                A.Superficie,
                A.NHabitaciones,
                A.NBanyos
            FROM Anuncios A
            INNER JOIN TiposViviendas TV
                ON A.TVivienda = TV.IdTVivienda
            INNER JOIN TiposAnuncios TA
                ON A.TAnuncio = TA.IdTAnuncio
            LEFT JOIN Paises P
                ON A.Pais = P.IdPais
            WHERE A.IdAnuncio = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        echo "<p>No se ha encontrado el anuncio.</p>";
        $stmt->close();
        $conn->close();
        return;
    }

    $anuncio = $resultado->fetch_assoc();
    $stmt->close();
    $sqlFotos = "SELECT
                    IdFoto,
                    Titulo,
                    Foto,
                    Alternativo
                 FROM Fotos
                 WHERE Anuncio = ?
                 ORDER BY IdFoto ASC";

    $stmtFotos = $conn->prepare($sqlFotos);
    $stmtFotos->bind_param("i", $id);
    $stmtFotos->execute();

    $resultadoFotos = $stmtFotos->get_result();

    // Guardar todas las fotografías
    $fotos = [];

    while ($foto = $resultadoFotos->fetch_assoc()) {
        $fotos[] = $foto;
    }

    // Calcular el número total de fotografías
    $totalFotos = count($fotos);

    if (!empty($anuncio["FPrincipal"])) {
        $totalFotos++;
    }

    ?>

    <section>

        <h1>
            <?= htmlspecialchars($anuncio["Titulo"], ENT_QUOTES, "UTF-8") ?>
        </h1>

        <!-- INFORMACIÓN BÁSICA DEL ANUNCIO -->

        <h2>Información del anuncio</h2>

        <p>
            <strong>Identificador:</strong>
            <?= (int)$anuncio["IdAnuncio"] ?>
        </p>

        <p>
            <strong>Tipo de vivienda:</strong>
            <?= htmlspecialchars($anuncio["TipoVivienda"], ENT_QUOTES, "UTF-8") ?>
        </p>

        <p>
            <strong>Tipo de anuncio:</strong>
            <?= htmlspecialchars($anuncio["TipoAnuncio"], ENT_QUOTES, "UTF-8") ?>
        </p>

        <p>
            <strong>Precio:</strong>
            <?= htmlspecialchars((string)$anuncio["Precio"], ENT_QUOTES, "UTF-8") ?> €
        </p>

        <p>
            <strong>Ciudad:</strong>
            <?= htmlspecialchars($anuncio["Ciudad"] ?? "", ENT_QUOTES, "UTF-8") ?>
        </p>

        <p>
            <strong>País:</strong>
            <?= htmlspecialchars($anuncio["Pais"] ?? "", ENT_QUOTES, "UTF-8") ?>
        </p>

        <p>
            <strong>Superficie:</strong>
            <?= htmlspecialchars((string)$anuncio["Superficie"], ENT_QUOTES, "UTF-8") ?> m²
        </p>

        <p>
            <strong>Habitaciones:</strong>
            <?= htmlspecialchars((string)$anuncio["NHabitaciones"], ENT_QUOTES, "UTF-8") ?>
        </p>

        <p>
            <strong>Baños:</strong>
            <?= htmlspecialchars((string)$anuncio["NBanyos"], ENT_QUOTES, "UTF-8") ?>
        </p>

        <hr>

        <!-- TOTAL DE FOTOGRAFÍAS -->

        <h2>
            Fotografías del anuncio
        </h2>

        <p>
            <strong>
                Número total de fotografías:
                <?= $totalFotos ?>
            </strong>
        </p>

        

        <div class="galeria">

            <?php if (!empty($anuncio["FPrincipal"])): ?>

                <div class="foto">

                    <img
                        src="<?= htmlspecialchars($anuncio["FPrincipal"], ENT_QUOTES, "UTF-8") ?>"
                        alt="<?= htmlspecialchars($anuncio["Alternativo"], ENT_QUOTES, "UTF-8") ?>"
                    >

                    <p><strong>Fotografía principal</strong></p>

                </div>

            <?php endif; ?>

            <?php foreach ($fotos as $foto): ?>

                <div class="foto">

                    <img
                        src="<?= htmlspecialchars($foto["Foto"], ENT_QUOTES, "UTF-8") ?>"
                        alt="<?= htmlspecialchars($foto["Alternativo"], ENT_QUOTES, "UTF-8") ?>"
                    >

                    <p>
                        <?= htmlspecialchars($foto["Titulo"] ?? "", ENT_QUOTES, "UTF-8") ?>
                    </p>

                </div>

            <?php endforeach; ?>

            <?php if ($totalFotos === 0): ?>

                <p>Este anuncio todavía no contiene fotografías.</p>

            <?php endif; ?>

        </div>

    </section>

    <?php

    $stmtFotos->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Fotografías del anuncio</title>

    <style>

        .galeria {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }

        .foto {
            width: 250px;
            text-align: center;
        }

        .foto img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }

    </style>

</head>

<body>

    <h1>Galería del anuncio</h1>

    <?php ReadData(); ?>

    <p>
        <a href="javascript:history.back()">Volver</a>
    </p>

</body>

</html>
