<?php
require_once "permitidos.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

$usuario = $_POST["loginUser"] ?? "";
$contrasena = $_POST["loginPass"] ?? "";

if (trim($usuario) === "" || trim($contrasena) === "") {
    header("Location: index.php?error=vacio");
    exit();
}

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

if ($accesoPermitido) {
    header("Location: partePrivada.php");
    exit();
} else {
    header("Location: index.php?error=incorrecto");
    exit();
}
?>