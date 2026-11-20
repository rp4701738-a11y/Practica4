<?php
require_once "permitidos.php";
require_once "conexion.php";
require_once "selectPaises.php";

protegerRuta();

$paises = [];
$idsPaisesValidos = [];
$datosFormulario = [
    'username' => '',
    'email' => '',
    'sexo' => '',
    'fechaNacimiento' => '',
    'ciudad' => '',
    'pais' => '',
    'fotoActual' => ''
];
$erroresValidacion = [];
$avisoValidacion = "";
$errorBD = "";
$idUsuarioActual = isset($_SESSION["idUsuario"]) ? (int)$_SESSION["idUsuario"] : 0;

$mapaSexo = [
    0 => 'Hombre',
    1 => 'Mujer',
    2 => 'Otro'
];

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = null;
$stmt = null;

try {
    $conn = OpenConnection();
    $paises = obtenerPaises($conn);
    foreach ($paises as $p) {
        $idsPaisesValidos[] = (int)$p['IdPais'];
    }

    if ($idUsuarioActual > 0) {
        $stmt = $conn->prepare("SELECT IdUsuario, NomUsuario, Email, Sexo, FNacimiento, Ciudad, Pais, Foto FROM Usuarios WHERE IdUsuario = ?");
        $stmt->bind_param("i", $idUsuarioActual);
    } else {
        $stmt = $conn->prepare("SELECT IdUsuario, NomUsuario, Email, Sexo, FNacimiento, Ciudad, Pais, Foto FROM Usuarios WHERE NomUsuario = ?");
        $stmt->bind_param("s", $_SESSION["usuario"]);
    }

    $stmt->execute();
    $res = $stmt->get_result();
    if ($usuario = $res->fetch_assoc()) {
        $datosFormulario['username'] = (string)$usuario['NomUsuario'];
        $datosFormulario['email'] = (string)$usuario['Email'];
        $datosFormulario['sexo'] = ($usuario['Sexo'] !== null && isset($mapaSexo[(int)$usuario['Sexo']])) ? $mapaSexo[(int)$usuario['Sexo']] : '';
        $datosFormulario['fechaNacimiento'] = (string)($usuario['FNacimiento'] ?? '');
        $datosFormulario['ciudad'] = (string)($usuario['Ciudad'] ?? '');
        $datosFormulario['pais'] = (string)($usuario['Pais'] ?? '');
        $datosFormulario['fotoActual'] = (string)($usuario['Foto'] ?? '');
    } else {
        $errorBD = "No se han encontrado los datos del usuario actual.";
    }
    $res->close();
} catch (mysqli_sql_exception $e) {
    error_log("Error en misDatos.php: " . $e->getMessage());
    $errorBD = "Error al recuperar tus datos desde la base de datos.";
} finally {
    if ($stmt instanceof mysqli_stmt) {
        $stmt->close();
    }
    if ($conn instanceof mysqli) {
        $conn->close();
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_POST["actualizar_estilos"]) && $errorBD === "") {
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $repeatPassword = (string)($_POST['repeatPassword'] ?? '');
    $email = trim((string)($_POST['email'] ?? ''));
    $sexo = trim((string)($_POST['sexo'] ?? ''));
    $fechaNacimiento = trim((string)($_POST['fechaNacimiento'] ?? ''));
    $ciudad = trim((string)($_POST['ciudad'] ?? ''));
    $pais = trim((string)($_POST['pais'] ?? ''));

    $datosFormulario['username'] = $username;
    $datosFormulario['email'] = $email;
    $datosFormulario['sexo'] = $sexo;
    $datosFormulario['fechaNacimiento'] = $fechaNacimiento;
    $datosFormulario['ciudad'] = $ciudad;
    $datosFormulario['pais'] = $pais;

    if ($username === '' || mb_strlen($username, 'UTF-8') < 3 || mb_strlen($username, 'UTF-8') > 15) {
        $erroresValidacion[] = "El nombre de usuario debe tener entre 3 y 15 caracteres.";
    }
    if ($password !== '' || $repeatPassword !== '') {
        if (mb_strlen($password, 'UTF-8') < 6 || mb_strlen($password, 'UTF-8') > 15) {
            $erroresValidacion[] = "La contraseña debe tener entre 6 y 15 caracteres.";
        }
        if ($password !== $repeatPassword) {
            $erroresValidacion[] = "Las contraseñas no coinciden.";
        }
    }
    if ($email === '' || mb_strlen($email, 'UTF-8') > 254 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erroresValidacion[] = "El correo electrónico no es válido.";
    }
    if ($fechaNacimiento !== '') {
        $dt = DateTime::createFromFormat('Y-m-d', $fechaNacimiento);
        if (!$dt || $dt->format('Y-m-d') !== $fechaNacimiento) {
            $erroresValidacion[] = "La fecha de nacimiento debe tener el formato AAAA-MM-DD.";
        }
    }
    if ($pais !== '') {
        $idPaisInt = filter_var($pais, FILTER_VALIDATE_INT);
        if ($idPaisInt === false || !in_array($idPaisInt, $idsPaisesValidos, true)) {
            $erroresValidacion[] = "El país seleccionado no es válido.";
        }
    }

    if (empty($erroresValidacion)) {
        $avisoValidacion = "Datos validados correctamente. La actualización en la base de datos se habilitará en la próxima práctica.";
    }
}

$actionFormulario = "misDatos.php";
$textoBoton = "Guardar cambios";
session_write_close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis datos</title>
    <link rel="stylesheet" href="style/signUp.css">
    <link rel="stylesheet" href="style/barraNav.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="javascript/signUp.js"></script>
    <style>
        .error-msg { color: red; font-size: 0.9em; display: none; margin-left: 10px; }
        .input-error { border: 2px solid red; background-color: #ffe6e6; }
    </style>
</head>
<body>
    <h3 id="url-impresion">URL: https://buscasa.gt.tc/misDatos.php</h3>
    <nav>
        <ul>
            <li><a href="index2.php" id="rgr"><span>Menú principal</span></a></li>
            <li><a href="busqueda.php" id="rgr"><span><i class="fa-solid fa-magnifying-glass"></i> Búsqueda</span></a></li>
            <li><a href="partePrivada.php" id="rgr"><span>Perfil (<?= obtenerSaludo() ?>)</span></a></li>
            <li><a href="misDatos.php?salir=1" id="lgn"><span>Salir</span></a></li>
        </ul>
    </nav>

    <?php mostrarBarraEstilos(); ?>

    <input type="checkbox" id="print-toggle" style="display: none;">
    <label for="print-toggle" style="display: block; margin-bottom: 10px; color: black; cursor: pointer; clear: both;">
        <u>Cambiar vista normal/impresión</u>
    </label>

    <section id="signIn">
        <h1>Mis datos:</h1>

        <?php if ($errorBD !== ""): ?>
            <p style="color: red; font-weight: bold;"><?= htmlspecialchars($errorBD, ENT_QUOTES, 'UTF-8') ?></p>
        <?php else: ?>
            <?php if (!empty($erroresValidacion)): ?>
                <div style="border: 2px solid red; padding: 12px; background-color: #ffe6e6; margin-bottom: 15px;">
                    <p style="color: red; font-weight: bold;">Se han encontrado errores en los datos introducidos:</p>
                    <ul>
                        <?php foreach ($erroresValidacion as $err): ?>
                            <li style="color: red;"><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($avisoValidacion !== ""): ?>
                <div style="border: 2px solid green; padding: 12px; background-color: #e6ffe6; margin-bottom: 15px;">
                    <p style="color: green; font-weight: bold;"><?= htmlspecialchars($avisoValidacion, ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            <?php endif; ?>

            <?php include "formUsuario.php"; ?>
        <?php endif; ?>
    </section>

    <footer>
        <br><br>
        <hr size="1px" align="center" color="black">
        <p>© 2026. Aviso de copyright: Contenido de uso libre.</p>
    </footer>
</body>
</html>