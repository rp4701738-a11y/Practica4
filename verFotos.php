
<?php

function OpenConnection()
{
    $serverName = "tcp:<server>.database.windows.net,1433";

    $connectionOptions = array(
        "Database" => "AdventureWorks",
        "Uid" => "<user_id>",
        "PWD" => "<password>",
        "Encrypt" => true,
        "TrustServerCertificate" => false
    );

    $conn = sqlsrv_connect($serverName, $connectionOptions);

    if ($conn === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    return $conn;
}

function ReadData()
{
    $Id = $_GET["Id"] ?? null;

    if ($Id === null || !filter_var($Id, FILTER_VALIDATE_INT)) {
        die("ID de anuncio no válido.");
    }

    $conn = OpenConnection();

    $tsql = "SELECT
                [Titulo],
                [Foto],
                [TVivienda],
                [Texto],
                [TAnuncio],
                [FPrincipal],
                [ALternativo],
                [Precio],
                [Ciudad],
                [Pais],
                [Superficie],
                [NHabitaciones],
                [NBanyos],
                [Planta],
                [FRegistro],
                [Usuario]
             FROM Fotos
             WHERE IdAnuncio = ?";

    $params = array($Id);

    $getProducts = sqlsrv_query($conn, $tsql, $params);

    if ($getProducts === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    while ($row = sqlsrv_fetch_array($getProducts, SQLSRV_FETCH_ASSOC)) {

        echo "<br>";

        // 1. Titulo
        echo "<p>Título: " . htmlspecialchars($row["Titulo"] ?? "") . "</p>";

        // 2. Foto
        echo '<img src="' . htmlspecialchars($row["Foto"] ?? "") . '" width="200" alt="Foto del anuncio">';

        // 3. TVivienda
        echo "<p>Tipo de vivienda: " . htmlspecialchars($row["TVivienda"] ?? "") . "</p>";

        // 4. Texto
        echo "<p>Texto: " . htmlspecialchars($row["Texto"] ?? "") . "</p>";

        // 5. TAnuncio
        echo "<p>Tipo de anuncio: " . htmlspecialchars($row["TAnuncio"] ?? "") . "</p>";

        // 6. FPrincipal
        echo "<p>Descripción principal: " . htmlspecialchars($row["FPrincipal"] ?? "") . "</p>";

        // 7. ALternativo
        echo "<p>Texto alternativo: " . htmlspecialchars($row["ALternativo"] ?? "") . "</p>";

        // 8. Precio
        echo "<p>Precio: " . htmlspecialchars($row["Precio"] ?? "") . " €</p>";

        // 9. Ciudad
        echo "<p>Ciudad: " . htmlspecialchars($row["Ciudad"] ?? "") . "</p>";

        // 10. Pais
        echo "<p>País: " . htmlspecialchars($row["Pais"] ?? "") . "</p>";

        // 11. Superficie
        echo "<p>Superficie: " . htmlspecialchars($row["Superficie"] ?? "") . " m²</p>";

        // 12. NHabitaciones
        echo "<p>Número de habitaciones: " . htmlspecialchars($row["NHabitaciones"] ?? "") . "</p>";

        // 13. NBanyos
        echo "<p>Número de baños: " . htmlspecialchars($row["NBanyos"] ?? "") . "</p>";

        // 14. Planta
        echo "<p>Planta: " . htmlspecialchars($row["Planta"] ?? "") . "</p>";

        // 15. FRegistro
        echo "<p>Fecha de registro: " .
             (isset($row["FRegistro"]) && $row["FRegistro"] instanceof DateTime
                 ? $row["FRegistro"]->format("d/m/Y H:i")
                 : htmlspecialchars($row["FRegistro"] ?? "")) .
             "</p>";

        // 16. Usuario
        echo "<p>Usuario: " . htmlspecialchars($row["Usuario"] ?? "") . "</p>";

        echo "<hr>";
    }

    sqlsrv_free_stmt($getProducts);
    sqlsrv_close($conn);
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
</head>

<body>

    <section>
        <?php ReadData(); ?>
    </section>

</body>
</html>
