<?php
require_once "permitidos.php";

function e($value) {
    if ($value === null) {
        return '';
    }
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_POST["actualizar_estilos"])) {
    $errores = [];

    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $repeatPassword = isset($_POST['repeatPassword']) ? $_POST['repeatPassword'] : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $sexo = isset($_POST['sexo']) ? trim($_POST['sexo']) : '';
    $fechaNacimiento = isset($_POST['fechaNacimiento']) ? trim($_POST['fechaNacimiento']) : '';
    $ciudad = isset($_POST['ciudad']) ? trim($_POST['ciudad']) : '';
    $pais = isset($_POST['pais']) ? trim($_POST['pais']) : '';

    if (empty($username)) {
        $errores[] = "El nombre de usuario es obligatorio.";
    }
    if (empty($password)) {
        $errores[] = "La contraseña es obligatoria.";
    }
    if (empty($repeatPassword)) {
        $errores[] = "Debe repetir la contraseña.";
    }
    if (!empty($password) && !empty($repeatPassword) && $password !== $repeatPassword) {
        $errores[] = "Las contraseñas no coinciden.";
    }

    if (!empty($errores)) {
        $_SESSION["flash"]["errores_registro"] = $errores;
        $_SESSION["flash"]["datos_registro"] = [
            "username" => $username,
            "email" => $email,
            "sexo" => $sexo,
            "fechaNacimiento" => $fechaNacimiento,
            "ciudad" => $ciudad,
            "pais" => $pais
        ];
        session_write_close();
        header("Location: signUp.php");
        exit();
    }

    $_SESSION["ultimo_registro_ok"] = [
        "username" => $username,
        "email" => $email,
        "sexo" => $sexo,
        "fechaNacimiento" => $fechaNacimiento,
        "ciudad" => $ciudad,
        "pais" => $pais
    ];
} elseif (isset($_SESSION["ultimo_registro_ok"])) {
    $reg = $_SESSION["ultimo_registro_ok"];
    $username = $reg["username"];
    $email = $reg["email"];
    $sexo = $reg["sexo"];
    $fechaNacimiento = $reg["fechaNacimiento"];
    $ciudad = $reg["ciudad"];
    $pais = $reg["pais"];
} else {
    session_write_close();
    header("Location: signUp.php");
    exit();
}

session_write_close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Respuesta Registro</title>
    <link rel="stylesheet" href="style/signUp.css">
    <link rel="stylesheet" href="style/barraNav.css">
</head>
<body>
    <?php mostrarBarraEstilos(); ?>
    <section id="respuesta">
        <h1>Resultado del Registro:</h1>
        <div class="exito-container" style="border: 2px solid green; padding: 15px; background-color: #e6ffe6;">
            <h3>Inserción realizada, tus datos son:</h3>
            <ul>
                <li><strong>Nombre de usuario:</strong> <?= e($username) ?></li>
                <li><strong>Email:</strong> <?= e($email) ?></li>
                <li><strong>Sexo:</strong> <?= e($sexo) ?></li>
                <li><strong>Fecha de nacimiento:</strong> <?= e($fechaNacimiento) ?></li>
                <li><strong>Ciudad:</strong> <?= e($ciudad) ?></li>
                <li><strong>País:</strong> <?= e($pais) ?></li>
            </ul>
            <p><em>Nota sobre la foto: No se muestra actualmente porque la subida de ficheros al servidor se implementará en una próxima práctica.</em></p>
            <p><a href="index.php">Ir a la página principal</a></p>
        </div>
    </section>
</body>
</html>
