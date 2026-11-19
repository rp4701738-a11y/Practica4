<?php
require_once "permitidos.php";

// =====================================================
// CONEXIÓN A MYSQL
// =====================================================

function OpenConnection()
{
    $conn = new mysqli("localhost", "wwwdata", "abc", "pibd");

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");

    return $conn;
}

// =====================================================
// COMPROBAR MÉTODO POST
// =====================================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    session_write_close();
    header("Location: index.php");
    exit();
}

// =====================================================
// RECUPERAR DATOS
// =====================================================

$usuario = trim($_POST["loginUser"] ?? "");
$contrasena = $_POST["loginPass"] ?? "";

$recordarme = isset($_POST["recordarme"]) &&
              $_POST["recordarme"] === "1";

$origen = ($_POST["origen"] ?? "index") === "signIn"
          ? "signIn.php"
          : "index.php";

// =====================================================
// VALIDAR CAMPOS
// =====================================================

if ($usuario === "" || $contrasena === "") {

    $_SESSION["flash"]["error"] =
        "Debes introducir un usuario y una contraseña.";

    session_write_close();
    header("Location: " . $origen);
    exit();
}

// =====================================================
// CONECTAR CON MYSQL
// =====================================================

$conn = OpenConnection();

// =====================================================
// BUSCAR USUARIO
// =====================================================

$sql = "SELECT
            IdUsuario,
            NomUsuario,
            Clave,
            Email,
            Foto,
            FRegistro,
            Estilo
        FROM Usuarios
        WHERE NomUsuario = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error al preparar consulta: " . $conn->error);
}

$stmt->bind_param("s", $usuario);
$stmt->execute();

$resultado = $stmt->get_result();

$usuarioEncontrado = $resultado->fetch_assoc();

$stmt->close();

// =====================================================
// COMPROBAR CONTRASEÑA
// =====================================================

$claveCorrecta = false;

if ($usuarioEncontrado !== null) {

    $claveGuardada = $usuarioEncontrado["Clave"];

    // Comprobar contraseña cifrada
    if (password_verify($contrasena, $claveGuardada)) {

        $claveCorrecta = true;

    }
    // Comprobar contraseña antigua en texto plano
    elseif (hash_equals($claveGuardada, $contrasena)) {

        // Convertir contraseña a hash
        $nuevoHash = password_hash(
            $contrasena,
            PASSWORD_DEFAULT
        );

        $stmtUpdate = $conn->prepare(
            "UPDATE Usuarios SET Clave = ? WHERE IdUsuario = ?"
        );

        $stmtUpdate->bind_param(
            "si",
            $nuevoHash,
            $usuarioEncontrado["IdUsuario"]
        );

        $stmtUpdate->execute();
        $stmtUpdate->close();

        $claveCorrecta = true;
    }
}

$conn->close();

// =====================================================
// INICIAR SESIÓN Y REDIRIGIR
// =====================================================

if ($claveCorrecta) {

    session_regenerate_id(true);

    $_SESSION["usuario"] = $usuarioEncontrado["NomUsuario"];
    $_SESSION["nombre"] = $usuarioEncontrado["NomUsuario"];
    $_SESSION["estilo"] = $usuarioEncontrado["Estilo"];
    $_SESSION["idUsuario"] = $usuarioEncontrado["IdUsuario"];

    session_write_close();

    header("Location: partePrivada.php");
    exit();

} else {

    $_SESSION["flash"]["error"] =
        "El usuario o la contraseña son incorrectos.";

    session_write_close();

    header("Location: " . $origen);
    exit();
}

$conn->close();

// =====================================================
// INICIAR SESIÓN SI