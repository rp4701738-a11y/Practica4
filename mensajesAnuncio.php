<?php
require_once "permitidos.php";
require_once "conexion.php";

protegerRuta();

$idParam = $_GET['id'] ?? null;
$idAnuncio = filter_var($idParam, FILTER_VALIDATE_INT);

$anuncio = null;
$mensajes = [];
$errorMensaje = "";
$idUsuarioActual = isset($_SESSION["idUsuario"]) ? (int)$_SESSION["idUsuario"] : 0;

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = null;
$stmtUsr = null;
$stmtAn = null;
$stmtMsg = null;

if ($idAnuncio === false || $idAnuncio <= 0) {
    $errorMensaje = "El identificador del anuncio no es válido.";
} else {
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

        $sqlAn = "SELECT
                    A.IdAnuncio,
                    A.Titulo,
                    A.Ciudad,
                    A.Precio,
                    A.FPrincipal,
                    A.Alternativo,
                    A.Usuario,
                    P.NomPais AS Pais
                  FROM Anuncios A
                  LEFT JOIN Paises P ON A.Pais = P.IdPais
                  WHERE A.IdAnuncio = ?";

        $stmtAn = $conn->prepare($sqlAn);
        $stmtAn->bind_param("i", $idAnuncio);
        $stmtAn->execute();
        $resAn = $stmtAn->get_result();
        $anuncio = $resAn->fetch_assoc();
        $resAn->close();

        if ($anuncio === null) {
            $errorMensaje = "No se ha encontrado el anuncio solicitado.";
        } elseif ((int)$anuncio['Usuario'] !== $idUsuarioActual) {
            $errorMensaje = "No tienes permiso para ver los mensajes de este anuncio.";
            $anuncio = null;
        } else {
            $sqlMsg = "SELECT
                        M.IdMensaje,
                        TM.NomTMensaje AS TipoMensaje,
                        M.Texto,
                        M.FRegistro,
                        U.IdUsuario AS IdRemitente,
                        U.NomUsuario AS Remitente
                       FROM Mensajes M
                       INNER JOIN TiposMensajes TM ON M.TMensaje = TM.IdTMensaje
                       INNER JOIN Usuarios U ON M.UsuOrigen = U.IdUsuario
                       WHERE M.Anuncio = ?
                       ORDER BY M.FRegistro DESC";

            $stmtMsg = $conn->prepare($sqlMsg);
            $stmtMsg->bind_param("i", $idAnuncio);
            $stmtMsg->execute();
            $resMsg = $stmtMsg->get_result();
            while ($fila = $resMsg->fetch_assoc()) {
                $mensajes[] = $fila;
            }
            $resMsg->close();
        }
    } catch (mysqli_sql_exception $e) {
        error_log("Error en mensajesAnuncio.php: " . $e->getMessage());
        $errorMensaje = "Error al cargar los mensajes del anuncio.";
    } finally {
        if ($stmtUsr instanceof mysqli_stmt) {
            $stmtUsr->close();
        }
        if ($stmtAn instanceof mysqli_stmt) {
            $stmtAn->close();
        }
        if ($stmtMsg instanceof mysqli_stmt) {
            $stmtMsg->close();
        }
        if ($conn instanceof mysqli) {
            $conn->close();
        }
    }
}

session_write_close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajes del anuncio</title>
    <link rel="stylesheet" href="style/barraNav.css">
    <link rel="stylesheet" href="style/mensajes.css">
    <link rel="stylesheet" href="style/titulos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <h3>URL: https://buscasa.gt.tc/mensajesAnuncio.php</h3>
    <ul>
        <li><a href="index2.php" id="rgr"><span>Menú principal</span></a></li>
        <li><a href="busqueda.php" id="rgr"><span><i class="fa-solid fa-magnifying-glass"></i> Búsqueda</span></a></li>
        <li><a href="partePrivada.php" id="rgr"><span>Perfil (<?= obtenerSaludo() ?>)</span></a></li>
        <li><a href="mensajesAnuncio.php?salir=1" id="lgn"><span>Salir</span></a></li>
    </ul>

    <?php mostrarBarraEstilos(); ?>

    <input type="checkbox" id="print-toggle" style="display: none;">
    <label for="print-toggle" style="display: block; margin-bottom: 10px; color: black; cursor: pointer; clear: both;">
        <u>Cambiar vista normal/impresión</u>
    </label>

    <h2>Mensajes del anuncio</h2>

    <?php if ($errorMensaje !== ""): ?>
        <p style="color: red; font-weight: bold;"><?= htmlspecialchars($errorMensaje, ENT_QUOTES, 'UTF-8') ?></p>
        <p><a href="misAnuncios.php">Volver a mis anuncios</a></p>
    <?php else: ?>
        <section>
            <h3><?= htmlspecialchars((string)$anuncio['Titulo'], ENT_QUOTES, 'UTF-8') ?></h3>
            <?php if (!empty($anuncio['FPrincipal'])): ?>
                <img src="<?= htmlspecialchars((string)$anuncio['FPrincipal'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars((string)$anuncio['Alternativo'], ENT_QUOTES, 'UTF-8') ?>" style="width: 220px; height: auto;">
            <?php endif; ?>
            <p>
                <strong>Ciudad:</strong> <?= htmlspecialchars((string)($anuncio['Ciudad'] ?? ''), ENT_QUOTES, 'UTF-8') ?> |
                <strong>País:</strong> <?= htmlspecialchars((string)($anuncio['Pais'] ?? ''), ENT_QUOTES, 'UTF-8') ?> |
                <strong>Precio:</strong> <?= htmlspecialchars(number_format((float)$anuncio['Precio'], 2, ',', '.'), ENT_QUOTES, 'UTF-8') ?> €
            </p>
            <p><strong>Total de mensajes recibidos:</strong> <?= count($mensajes) ?></p>
        </section>

        <?php if (count($mensajes) === 0): ?>
            <p>Este anuncio todavía no ha recibido ningún mensaje.</p>
        <?php else: ?>
            <?php foreach ($mensajes as $msg): ?>
                <div>
                    <p>
                        Tipo: <?= htmlspecialchars((string)$msg['TipoMensaje'], ENT_QUOTES, 'UTF-8') ?><br>
                        Fecha: <?= htmlspecialchars(date('d/m/Y H:i', strtotime((string)$msg['FRegistro'])), ENT_QUOTES, 'UTF-8') ?><br>
                        Usuario: <a href="perfil.php?id=<?= (int)$msg['IdRemitente'] ?>"><?= htmlspecialchars((string)$msg['Remitente'], ENT_QUOTES, 'UTF-8') ?></a>
                    </p>
                    <p class="negro">
                        Mensaje: <?= nl2br(htmlspecialchars((string)$msg['Texto'], ENT_QUOTES, 'UTF-8')) ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <p><a href="verAnuncio.php?id=<?= (int)$anuncio['IdAnuncio'] ?>">Volver al anuncio</a></p>
    <?php endif; ?>

    <footer>
        <br><br>
        <hr size="1px" align="center" color="black">
        <p>© 2026. Aviso de copyright: Contenido de uso libre.</p>
    </footer>
</body>
</html>