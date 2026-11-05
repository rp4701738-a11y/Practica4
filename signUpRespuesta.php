<?php
function e($value) {
    if ($value === null) {
        return '';
    }
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$errores = [];

// Recogemos los datos enviados por el formulario (requiere que añadas method="post" al <form> de tu HTML)
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Respuesta Registro</title>
    <link rel="stylesheet" href="style/signUp.css">
</head>
<body>
    <section id="respuesta">
        <h1>Resultado del Registro:</h1>
        
        <?php if (!empty($errores)): ?>
            <!-- Estrategia 1: Mostrar errores y enlace de vuelta -->
            <div class="error-container" style="border: 2px solid red; padding: 15px; background-color: #ffe6e6;">
                <h3>Se han encontrado errores en los datos introducidos:</h3>
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?= e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
                <p><a href="signUp.php">Volver al formulario inicial</a></p>
            </div>
        <?php else: ?>
            <!-- Visualización de datos si la validación es correcta -->
            <div class="exito-container" style="border: 2px solid green; padding: 15px; background-color: #e6ffe6;">
                <h3>¡Inserción realizada, tus datos son:</h3>
                <ul>
                    <li><strong>Nombre de usuario:</strong> <?= e($username) ?></li>
                    <li><strong>Email:</strong> <?= e($email) ?></li>
                    <li><strong>Sexo:</strong> <?= e($sexo) ?></li>
                    <li><strong>Fecha de nacimiento:</strong> <?= e($fechaNacimiento) ?></li>
                    <li><strong>Ciudad:</strong> <?= e($ciudad) ?></li>
                    <li><strong>País:</strong> <?= e($pais) ?></li>
                </ul>
                <p><em>Nota sobre la foto: No se muestra actualmente porque la subida de ficheros al servidor se implementará en una próxima práctica.</em></p>
            </div>
        <?php endif; ?>
    </section>
</body>
</html>