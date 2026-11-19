
<?php
require_once "permitidos.php";
require_once "conexion.php";

protegerRuta();

function ReadData()
{
    $id = $_GET["id"] ?? null;

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

    $usuario = $_SESSION["usuario"];

    // Consultar información del anuncio y comprobar propietario
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
            INNER JOIN Usuarios U
                ON A.Usuario = U.IdUsuario
            INNER JOIN TiposViviendas TV
                ON A.TVivienda = TV.IdTVivienda
            INNER JOIN TiposAnuncios TA
                ON A.TAnuncio = TA.IdTAnuncio
            LEFT JOIN Paises P
                ON A.Pais = P.IdPais
            WHERE A.IdAnuncio = ?
              AND U.NomUsuario = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $id, $usuario);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        echo "<p>El anuncio no existe o no te pertenece.</p>";

        $stmt->close();
        $conn->close();
        return;
    }

    $anuncio = $resultado->fetch_assoc();

    $stmt->close();

    // Consultar fotografías adicionales
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

    $fotos = [];

    while ($foto = $resultadoFotos->fetch_assoc()) {
        $fotos[] = $foto;
    }

    // Calcular total de fotografías
    $totalFotos = count($fotos);

    if (!empty($anuncio["FPrincipal"])) {
        $totalFotos++;
    }

    ?>

    <h1>
        <?= htmlspecialchars($anuncio["Titulo"], ENT_QUOTES, "UTF-8") ?>
    </h1>

    <h2>Información básica del anuncio</h2>

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

    <h2>Fotografías del anuncio</h2>

    <p>
        <strong>Número total de fotografías: <?= $totalFotos ?></strong>
    </p>

    <div class="galeria">

        <!-- Fotografía principal -->

        <?php if (!empty($anuncio["FPrincipal"])): ?>

            <div class="foto">

                <img
                    src="<?= htmlspecialchars($anuncio["FPrincipal"], ENT_QUOTES, "UTF-8") ?>"
                    alt="<?= htmlspecialchars($anuncio["Alternativo"], ENT_QUOTES, "UTF-8") ?>">

                <p><strong>Fotografía principal</strong></p>

            </div>

        <?php endif; ?>

        <!-- Fotografías adicionales -->

        <?php foreach ($fotos as $foto): ?>

            <div class="foto">

                <img
                    src="<?= htmlspecialchars($foto["Foto"], ENT_QUOTES, "UTF-8") ?>"
                    alt="<?= htmlspecialchars($foto["Alternativo"], ENT_QUOTES, "UTF-8") ?>">

                <p>
                    <?= htmlspecialchars($foto["Titulo"] ?? "", ENT_QUOTES, "UTF-8") ?>
                </p>

            </div>

        <?php endforeach; ?>

        <?php if ($totalFotos === 0): ?>

            <p>Este anuncio no tiene fotografías.</p>

        <?php endif; ?>

    </div>

    <p>
        <a href="misAnuncios.php">Volver a mis anuncios</a>
    </p>

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

    <link rel="stylesheet" href="style/partePrivada.css">
    <link rel="stylesheet" href="style/barraNav.css">

    <style>
        .galeria {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin: 20px 0;
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

    <nav>
        <ul>
            <li>
                <a href="index2.php">Menú principal</a>
            </li>

            <li>
                <a href="partePrivada.php">
                    Perfil (<?= htmlspecialchars(obtenerSaludo(), ENT_QUOTES, 'UTF-8') ?>)
                </a>
            </li>

            <li>
                <a href="misAnuncios.php?salir=1">Salir</a>
            </li>
        </ul>
    </nav>

    <?php mostrarBarraEstilos(); ?>

    <?php ReadData(); ?>

</body>
</html>
