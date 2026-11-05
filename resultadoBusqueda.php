
<?php

// Recoger la ciudad de la página anterior
$ciudad = $_GET['ciudad'] ?? '';

// Ejemplo de anuncios
$anuncios = [
    [
        "id" => 1,
        "titulo" => "Casa en el Caribe",
        "fecha" => "20 de diciembre de 2025",
        "ciudad" => "Caribe",
        "pais" => "España",
        "precio" => "20000$",
        "imagen" => "imagenes/casa1.jpeg"
    ],
    [
        "id" => 2,
        "titulo" => "Apartamento en Murcia",
        "fecha" => "28 de diciembre de 2024",
        "ciudad" => "Murcia",
        "pais" => "España",
        "precio" => "15000$",
        "imagen" => "imagenes/apartamento1.jpeg"
    ]
];

$resultados = [];

foreach ($anuncios as $anuncio) {

    if ($ciudad == '' || strcasecmp($anuncio['ciudad'], $ciudad) == 0) {
        $resultados[] = $anuncio;
    }

}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Resultado</title>

    <link rel="stylesheet" href="style/sectionP.css">
    <link rel="stylesheet" href="style/barraNav.css">
    <link rel="stylesheet" href="style/resultadoBusqueda.css">
</head>

<body>

    <h3 id="url-impresion">
        URL: https://buscasa.gt.tc/resultadoBusqueda.php
    </h3>

    <h2>Resultados de la búsqueda</h2>

    <hr size="1px" align="left" color="black">

    <br><br>

    <?php if (count($resultados) > 0) { ?>

        <?php foreach ($resultados as $anuncio) { ?>

            <section>

                <a href="anuncio.php?id=<?php echo $anuncio['id']; ?>">
                    <img src="<?php echo $anuncio['imagen']; ?>">
                </a>

                <p><?php echo $anuncio['titulo']; ?></p>

                <p><?php echo $anuncio['fecha']; ?></p>

                <p><?php echo $anuncio['ciudad']; ?></p>

                <p><?php echo $anuncio['pais']; ?></p>

                <p><?php echo $anuncio['precio']; ?></p>

            </section>

        <?php } ?>

    <?php } else { ?>

        <p>No se han encontrado anuncios en
            <?php echo htmlspecialchars($ciudad); ?>.
        </p>

    <?php } ?>

    <footer>
        <br><br>
        <hr size="1px" align="center" color="black">
        <p>&copy; 2026. Aviso de copyright: Contenido de uso libre.</p>
    </footer>

</body>
</html>
