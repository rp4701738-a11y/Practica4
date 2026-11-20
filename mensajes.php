<?php
require_once "permitidos.php";
require_once "conexion.php";

protegerRuta();

$mensajesEnviados = [];
$mensajesRecibidos = [];
$errorBD = "";
$idUsuarioActual = isset($_SESSION["idUsuario"]) ? (int)$_SESSION["idUsuario"] : 0;

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = null;
$stmtUsr = null;
$stmtEnv = null;
$stmtRec = null;

try {
    $conn = OpenConnection();

    if ($idUsuarioActual <= 0 && !empty($_SESSION["usuario"])) {
        $stmtUsr = $conn->prepare("SELECT IdUsuario FROM Usuarios WHERE NomUsuario = ?");
        $stmtUsr->bind_param("s", $_SESSION["usuario"]);
        $stmtUsr->execute();
        $resUsr = $stmtUsr->get_result();
        if ($filaUsr = $resUsr->fetch_assoc()) {
            $idUsuarioActual = (int)$filaUsr["IdUsuario"];
        }
        $resUsr->close();
    }

    if ($idUsuarioActual > 0) {
        $sqlEnviados = "SELECT
                            M.IdMensaje,
                            TM.NomTMensaje AS TipoMensaje,
                            M.Texto,
                            M.FRegistro,
                            U.IdUsuario AS IdOtroUsuario,
                            U.NomUsuario AS OtroUsuario,
                            A.IdAnuncio,
                            A.Titulo AS TituloAnuncio
                        FROM Mensajes M
                        INNER JOIN TiposMensajes TM ON M.TMensaje = TM.IdTMensaje
                        INNER JOIN Usuarios U ON M.UsuDestino = U.IdUsuario
                        INNER JOIN Anuncios A ON M.Anuncio = A.IdAnuncio
                        WHERE M.UsuOrigen = ?
                        ORDER BY M.FRegistro DESC";

        $stmtEnv = $conn->prepare($sqlEnviados);
        $stmtEnv->bind_param("i", $idUsuarioActual);
        $stmtEnv->execute();
        $resEnv = $stmtEnv->get_result();
        while ($fila = $resEnv->fetch_assoc()) {
            $mensajesEnviados[] = $fila;
        }
        $resEnv->close();

        $sqlRecibidos = "SELECT
                            M.IdMensaje,
                            TM.NomTMensaje AS TipoMensaje,
                            M.Texto,
                            M.FRegistro,
                            U.IdUsuario AS IdOtroUsuario,
                            U.NomUsuario AS OtroUsuario,
                            A.IdAnuncio,
                            A.Titulo AS TituloAnuncio
                        FROM Mensajes M
                        INNER JOIN TiposMensajes TM ON M.TMensaje = TM.IdTMensaje
                        INNER JOIN Usuarios U ON M.UsuOrigen = U.IdUsuario
                        INNER JOIN Anuncios A ON M.Anuncio = A.IdAnuncio
                        WHERE M.UsuDestino = ?
                        ORDER BY M.FRegistro DESC";

        $stmtRec = $conn->prepare($sqlRecibidos);
        $stmtRec->bind_param("i", $idUsuarioActual);
        $stmtRec->execute();
        $resRec = $stmtRec->get_result();
        while ($fila = $resRec->fetch_assoc()) {
            $mensajesRecibidos[] = $fila;
        }
        $resRec->close();
    }
} catch (mysqli_sql_exception $e) {
    error_log("Error en mensajes.php: " . $e->getMessage());
    $errorBD = "Error al cargar los mensajes desde la base de datos.";
} finally {
    if ($stmtUsr instanceof mysqli_stmt) {
        $stmtUsr->close();
    }
    if ($stmtEnv instanceof mysqli_stmt) {
        $stmtEnv->close();
    }
    if ($stmtRec instanceof mysqli_stmt) {
        $stmtRec->close();
    }
    if ($conn instanceof mysqli) {
        $conn->close();
    }
}

$totalEnviados = count($mensajesEnviados);
$totalRecibidos = count($mensajesRecibidos);

session_write_close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis mensajes</title>
    <link rel="stylesheet" href="style/barraNav.css">
    <link rel="stylesheet" href="style/mensajes.css">
    <link rel="stylesheet" href="style/titulos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <h3>URL: https://buscasa.gt.tc/mensajes.php</h3>
    <ul>
        <li><a href="index2.php" id="rgr"><span>Menú principal</span></a></li>
        <li><a href="busqueda.php" id="rgr"><span><i class="fa-solid fa-magnifying-glass"></i> Búsqueda</span></a></li>
        <li><a href="partePrivada.php" id="rgr"><span>Perfil (<?= obtenerSaludo() ?>)</span></a></li>
        <li><a href="mensajes.php?salir=1" id="lgn"><span>Salir</span></a></li>
    </ul>

    <?php mostrarBarraEstilos(); ?>

    <input type="checkbox" id="print-toggle" style="display: none;">
    <label for="print-toggle" style="display: block; margin-bottom: 10px; color: black; cursor: pointer; clear: both;">
        <u>Cambiar vista normal/impresión</u>
    </label>

    <h1>Mis mensajes</h1>

    <?php if ($errorBD !== ""): ?>
        <p style="color: red; font-weight: bold;"><?= htmlspecialchars($errorBD, ENT_QUOTES, 'UTF-8') ?></p>
    <?php else: ?>
        <p style="clear: both;">
            <strong>Total de mensajes enviados:</strong> <?= $totalEnviados ?> |
            <strong>Total de mensajes recibidos:</strong> <?= $totalRecibidos ?>
        </p>

        <hr size="1px" align="left" color="black" style="clear: both;">

        <h2 style="clear: both; margin-top: 20px;">Mensajes enviados (Total: <?= $totalEnviados ?>)</h2>
        <?php if ($totalEnviados === 0): ?>
            <p>No has enviado ningún mensaje todavía.</p>
        <?php else: ?>
            <?php foreach ($mensajesEnviados as $msg): ?>
                <div>
                    <p>
                        Tipo: <?= htmlspecialchars((string)$msg['TipoMensaje'], ENT_QUOTES, 'UTF-8') ?><br>
                        Fecha: <?= htmlspecialchars(date('d/m/Y H:i', strtotime((string)$msg['FRegistro'])), ENT_QUOTES, 'UTF-8') ?><br>
                        Destinatario: <a href="perfil.php?id=<?= (int)$msg['IdOtroUsuario'] ?>"><?= htmlspecialchars((string)$msg['OtroUsuario'], ENT_QUOTES, 'UTF-8') ?></a><br>
                        Anuncio: <a href="anuncio.php?id=<?= (int)$msg['IdAnuncio'] ?>"><?= htmlspecialchars((string)$msg['TituloAnuncio'], ENT_QUOTES, 'UTF-8') ?></a>
                    </p>
                    <p class="negro">
                        Mensaje: <?= nl2br(htmlspecialchars((string)$msg['Texto'], ENT_QUOTES, 'UTF-8')) ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <hr size="1px" align="left" color="black" style="clear: both; margin-top: 25px;">

        <h2 style="clear: both; margin-top: 20px;">Mensajes recibidos (Total: <?= $totalRecibidos ?>)</h2>
        <?php if ($totalRecibidos === 0): ?>
            <p>No has recibido ningún mensaje todavía.</p>
        <?php else: ?>
            <?php foreach ($mensajesRecibidos as $msg): ?>
                <div>
                    <p>
                        Tipo: <?= htmlspecialchars((string)$msg['TipoMensaje'], ENT_QUOTES, 'UTF-8') ?><br>
                        Fecha: <?= htmlspecialchars(date('d/m/Y H:i', strtotime((string)$msg['FRegistro'])), ENT_QUOTES, 'UTF-8') ?><br>
                        Remitente: <a href="perfil.php?id=<?= (int)$msg['IdOtroUsuario'] ?>"><?= htmlspecialchars((string)$msg['OtroUsuario'], ENT_QUOTES, 'UTF-8') ?></a><br>
                        Anuncio: <a href="anuncio.php?id=<?= (int)$msg['IdAnuncio'] ?>"><?= htmlspecialchars((string)$msg['TituloAnuncio'], ENT_QUOTES, 'UTF-8') ?></a>
                    </p>
                    <p class="negro">
                        Mensaje: <?= nl2br(htmlspecialchars((string)$msg['Texto'], ENT_QUOTES, 'UTF-8')) ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>

    <footer>
        <br><br>
        <hr size="1px" align="center" color="black">
        <p>© 2026. Aviso de copyright: Contenido de uso libre.</p>
    </footer>
</body>
</html>