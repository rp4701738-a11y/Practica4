<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
$anuncio = $_GET['anuncio'] ?? '';
?>
    <form action="">
        <label>Foto: <input type="file"></label>
        <label>Titulo: <input type="text"></label>
        <label>Texto alternativo: <input type="text"></label>
        <label>Anuncio al que añadirselo: <input type="text" value="<?php echo htmlspecialchars($anuncio); ?>"></label>
    </form>
</body>
</html>