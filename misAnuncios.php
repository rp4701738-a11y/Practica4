
<?php
$anuncios = [
    [
        "id" => 1,
        "titulo" => "Casa en la playa",
        "ciudad" => "Castilla",
        "pais" => "España",
        "precio" => 20000
    ],
    [
        "id" => 2,
        "titulo" => "Apartamento en la montaña",
        "ciudad" => "Madrid",
        "pais" => "España",
        "precio" => 35000
    ],
    [
        "id" => 3,
        "titulo" => "Casa rural",
        "ciudad" => "Valencia",
        "pais" => "España",
        "precio" => 15000
    ]
];
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

<?php foreach ($anuncios as $anuncio) { ?>

    <section>

        <a href="verAnuncio.php?id=<?php echo $anuncio['id']; ?>">
            <img src="imagenes/apartamento1.jpeg" width="50" height="50">
        </a>

        <h3><?php echo $anuncio['titulo']; ?></h3>

        <p>Ciudad: <?php echo $anuncio['ciudad']; ?></p>

        <p>País: <?php echo $anuncio['pais']; ?></p>

        <p>Precio: <?php echo $anuncio['precio']; ?> euros</p>

        <a href="anadirFoto.php?anuncio=<?php echo $anuncio['titulo']; ?>">
            Añadir foto
        </a>

    </section>

<?php } ?>

</body>
</html>
