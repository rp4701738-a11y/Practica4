<?php
require_once "permitidos.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    session_write_close();
    header("Location: index.php");
    exit();
}

$usuario = $_POST["loginUser"] ?? "";
$contrasena = $_POST["loginPass"] ?? "";
$recordarme = isset($_POST["recordarme"]);
$origen = ($_POST["origen"] ?? "index") === "signIn" ? "signIn.php" : "index.php";

if (trim($usuario) === "" || trim($contrasena) === "") {
    $_SESSION["flash"]["error"] = "Debes introducir un usuario y una contraseña.";
    session_write_close();
    header("Location: " . $origen);
    exit();
}

$usuarioEncontrado = null;
foreach ($usuarios as $datos) {
    if (trim($usuario) === $datos["usuario"] && $contrasena === $datos["contrasena"]) {
        $usuarioEncontrado = $datos;
        break;
    }
}

if ($usuarioEncontrado !== null) {
    $ahora = date("d/m/y \a \l\a\s H:i");

    $_SESSION["usuario"] = $usuarioEncontrado["usuario"];
    $_SESSION["nombre"] = $usuarioEncontrado["nombre"];
    $_SESSION["estilo"] = $usuarioEncontrado["estilo"];
    $_SESSION["ultima_visita"] = $_COOKIE["recordar_visita"] ?? $ahora;

    if ($recordarme) {
        $caduca90Dias = time() + (90 * 24 * 60 * 60);
        $_SESSION["recordado"] = true;
        setcookie("recordar_token", $usuarioEncontrado["token"], $caduca90Dias, "/", "", false, true);
        setcookie("recordar_visita", $ahora, $caduca90Dias, "/", "", false, true);
        setcookie("recordar_expira", (string)$caduca90Dias, $caduca90Dias, "/", "", false, true);
    } else {
        $_SESSION["recordado"] = false;
        setcookie("recordar_token", "", time() - 3600, "/", "", false, true);
        setcookie("recordar_visita", "", time() - 3600, "/", "", false, true);
        setcookie("recordar_expira", "", time() - 3600, "/", "", false, true);
    }

    session_write_close();
    header("Location: partePrivada.php");
    exit();
} else {
    $_SESSION["flash"]["error"] = "El usuario o la contraseña son incorrectos.";
    session_write_close();
    header("Location: " . $origen);
    exit();
}
?>