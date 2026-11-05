<?php
require_once "permitidos.php";

// Comprobar que el formulario se ha enviado mediante POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

// Recoger los datos del formulario
$usuario = $_POST["loginUser"] ?? "";
$contrasena = $_POST["loginPass"] ?? "";

// Comprobar que los campos no estén vacíos
if (trim($usuario) === "" || trim($contrasena) === "") {
    header("Location: index.php?error=vacio");
    exit();
}

// Comprobar si las credenciales son correctas
$accesoPermitido = false;

foreach ($usuarios as $datos) {
    if (
        trim($usuario) === $datos["usuario"] &&
        $contrasena === $datos["contrasena"]
    ) {
        $accesoPermitido = true;
        break;
    }
}

// Redirigir según el resultado
if ($accesoPermitido) {
    header("Location: partePrivada.php");
    exit();
} else {
    header("Location: index.php?error=incorrecto");
    exit();
}
?>