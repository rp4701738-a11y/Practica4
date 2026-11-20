<?php
require_once "permitidos.php";
require_once "conexion.php";

$enlaceInicio = isset($_SESSION["usuario"]) ? "index2.php" : "index.php";
$resultados = [];
$errores = [];
$errorBD = "";

$esAvanzada = isset($_GET['avanzada']) ||
    isset($_GET['tipo_anuncio']) ||
    isset($_GET['tipo_vivienda']) ||
    isset($_GET['pais']) ||
    isset($_GET['precio_min']) ||
    isset($_GET['precio_max']) ||
    isset($_GET['fecha_min']) ||
    isset($_GET['fecha_max']);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = null;
$stmt = null;

try {
    $conn = OpenConnection();

    $condiciones = [];
    $tipos = "";
    $parametros = [];

    if ($esAvanzada) {
        $tipoAnuncio = trim((string)($_GET['tipo_anuncio'] ?? ''));
        $tipoVivienda = trim((string)($_GET['tipo_vivienda'] ?? ''));
        $pais = trim((string)($_GET['pais'] ?? ''));
        $ciudad = trim((string)($_GET['ciudad'] ?? ''));
        $precioMin = trim((string)($_GET['precio_min'] ?? ''));
        $precioMax = trim((string)($_GET['precio_max'] ?? ''));
        $fechaMin = trim((string)($_GET['fecha_min'] ?? ''));
        $fechaMax = trim((string)($_GET['fecha_max'] ?? ''));

        if ($tipoAnuncio !== '') {
            $idTA = filter_var($tipoAnuncio, FILTER_VALIDATE_INT);
            if ($idTA === false || $idTA <= 0) {
                $errores[] = "El tipo de anuncio seleccionado no es válido.";
            } else {
                $condiciones[] = "A.TAnuncio = ?";
                $tipos .= "i";
                $parametros[] = $idTA;
            }
        }

        if ($tipoVivienda !== '') {
            $idTV = filter_var($tipoVivienda, FILTER_VALIDATE_INT);
            if ($idTV === false || $idTV <= 0) {
                $errores[] = "El tipo de vivienda seleccionado no es válido.";
            } else {
                $condiciones[] = "A.TVivienda = ?";
                $tipos .= "i";
                $parametros[] = $idTV;
            }
        }

        if ($pais !== '') {
            $idPais = filter_var($pais, FILTER_VALIDATE_INT);
            if ($idPais === false || $idPais <= 0) {
                $errores[] = "El país seleccionado no es válido.";
            } else {
                $condiciones[] = "A.Pais = ?";
                $tipos .= "i";
                $parametros[] = $idPais;
            }
        }

        if ($ciudad !== '') {
            $condiciones[] = "LOWER(A.Ciudad) LIKE CONCAT('%', LOWER(?), '%')";
            $tipos .= "s";
            $parametros[] = $ciudad;
        }

        $pMinVal = null;
        if ($precioMin !== '') {
            $pMinVal = filter_var($precioMin, FILTER_VALIDATE_FLOAT);
            if ($pMinVal === false || $pMinVal < 0) {
                $errores[] = "El precio mínimo debe ser un número mayor o igual que 0.";
                $pMinVal = null;
            } else {
                $condiciones[] = "A.Precio >= ?";
                $tipos .= "d";
                $parametros[] = $pMinVal;
            }
        }

        $pMaxVal = null;
        if ($precioMax !== '') {
            $pMaxVal = filter_var($precioMax, FILTER_VALIDATE_FLOAT);
            if ($pMaxVal === false || $pMaxVal < 0) {
                $errores[] = "El precio máximo debe ser un número mayor o igual que 0.";
                $pMaxVal = null;
            } else {
                $condiciones[] = "A.Precio <= ?";
                $tipos .= "d";
                $parametros[] = $pMaxVal;
            }
        }

        if ($pMinVal !== null && $pMaxVal !== null && $pMinVal > $pMaxVal) {
            $errores[] = "El precio mínimo no puede ser mayor que el precio máximo.";
        }

        $fMinOk = null;
        if ($fechaMin !== '') {
            $dtMin = DateTime::createFromFormat('Y-m-d', $fechaMin);
            if (!$dtMin || $dtMin->format('Y-m-d') !== $fechaMin) {
                $errores[] = "La fecha inicial no tiene un formato válido (AAAA-MM-DD).";
            } else {
                $fMinOk = $fechaMin;
                $condiciones[] = "DATE(A.FRegistro) >= ?";
                $tipos .= "s";
                $parametros[] = $fMinOk;
            }
        }

        $fMaxOk = null;
        if ($fechaMax !== '') {
            $dtMax = DateTime::createFromFormat('Y-m-d', $fechaMax);
            if (!$dtMax || $dtMax->format('Y-m-d') !== $fechaMax) {
                $errores[] = "La fecha final no tiene un formato válido (AAAA-MM-DD).";
            } else {
                $fMaxOk = $fechaMax;
                $condiciones[] = "DATE(A.FRegistro) <= ?";
                $tipos .= "s";
                $parametros[] = $fMaxOk;
            }
        }

        if ($fMinOk !== null && $fMaxOk !== null && $fMinOk > $fMaxOk) {
            $errores[] = "La fecha inicial no puede ser posterior a la fecha final.";
        }
    } else {
        $textoRapido = trim((string)($_GET['ciudad'] ?? $_GET['q'] ?? ''));

        if ($textoRapido !== '') {
            $viviendasBD = [];
            $resTV = $conn->query("SELECT NomTVivienda FROM TiposViviendas");
            while ($f = $resTV->fetch_assoc()) {
                $viviendasBD[] = mb_strtolower($f['NomTVivienda'], 'UTF-8');
            }
            $resTV->close();

            $anunciosBD = [];
            $resTA = $conn->query("SELECT NomTAnuncio FROM TiposAnuncios");
            while ($f = $resTA->fetch_assoc()) {
                $anunciosBD[] = mb_strtolower($f['NomTAnuncio'], 'UTF-8');
            }
            $resTA->close();

            $tokensBrutos = preg_split('/\s+/u', mb_strtolower($textoRapido, 'UTF-8'), -1, PREG_SPLIT_NO_EMPTY);
            $palabrasIgnoradas = ['un', 'una', 'en', 'de'];
            $tokens = [];
            foreach ($tokensBrutos as $tok) {
                if (!in_array($tok, $palabrasIgnoradas, true)) {
                    $tokens[] = $tok;
                }
            }

            $totalTokens = count($tokens);
            if ($totalTokens > 0) {
                $idx = 0;
                $tipoViviendaEncontrado = null;

                if ($idx + 1 < $totalTokens && in_array($tokens[$idx] . ' ' . $tokens[$idx + 1], $viviendasBD, true)) {
                    $tipoViviendaEncontrado = $tokens[$idx] . ' ' . $tokens[$idx + 1];
                    $idx += 2;
                } elseif (in_array($tokens[$idx], $viviendasBD, true)) {
                    $tipoViviendaEncontrado = $tokens[$idx];
                    $idx += 1;
                }

                if ($tipoViviendaEncontrado !== null) {
                    $condiciones[] = "LOWER(TV.NomTVivienda) = LOWER(?)";
                    $tipos .= "s";
                    $parametros[] = $tipoViviendaEncontrado;

                    if ($idx < $totalTokens) {
                        $tipoAnuncioEncontrado = null;
                        if ($idx + 1 < $totalTokens && in_array($tokens[$idx] . ' ' . $tokens[$idx + 1], $anunciosBD, true)) {
                            $tipoAnuncioEncontrado = $tokens[$idx] . ' ' . $tokens[$idx + 1];
                            $idx += 2;
                        } else {
                            $tipoAnuncioEncontrado = $tokens[$idx];
                            $idx += 1;
                        }
                        $condiciones[] = "LOWER(TA.NomTAnuncio) = LOWER(?)";
                        $tipos .= "s";
                        $parametros[] = $tipoAnuncioEncontrado;
                    }

                    if ($idx < $totalTokens) {
                        $ciudadRapida = implode(' ', array_slice($tokens, $idx));
                        $condiciones[] = "LOWER(A.Ciudad) LIKE CONCAT('%', LOWER(?), '%')";
                        $tipos .= "s";
                        $parametros[] = $ciudadRapida;
                    }
                } else {
                    $textoRestante = implode(' ', $tokens);
                    $condiciones[] = "(LOWER(TV.NomTVivienda) = LOWER(?) OR LOWER(A.Ciudad) LIKE CONCAT('%', LOWER(?), '%'))";
                    $tipos .= "ss";
                    $parametros[] = $textoRestante;
                    $parametros[] = $textoRestante;
                }
            }
        }
    }

    if (empty($errores)) {
        $sql = "SELECT
                    A.IdAnuncio,
                    A.Titulo,
                    A.FPrincipal,
                    A.Alternativo,
                    A.FRegistro,
                    A.Ciudad,
                    A.Precio,
                    P.NomPais AS Pais,
                    TA.NomTAnuncio AS TipoAnuncio,
                    TV.NomTVivienda AS TipoVivienda
                FROM Anuncios A
                INNER JOIN TiposAnuncios TA ON A.TAnuncio = TA.IdTAnuncio
                INNER JOIN TiposViviendas TV ON A.TVivienda = TV.IdTVivienda
                LEFT JOIN Paises P ON A.Pais = P.IdPais";

        if (!empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }

        $sql .= " ORDER BY A.FRegistro DESC";

        $stmt = $conn->prepare($sql);
        if ($tipos !== "") {
            $stmt->bind_param($tipos, ...$parametros);
        }
        $stmt->execute();
        $res = $stmt->get_result();
        while ($fila = $res->fetch_assoc()) {
            $resultados[] = $fila;
        }
        $res->close();
    }
} catch (mysqli_sql_exception $e) {
    error_log("Error en resultadoBusqueda.php: " . $e->getMessage());
    $errorBD = "Error al realizar la búsqueda en la base de datos.";
} finally {
    if ($stmt instanceof mysqli_stmt) {
        $stmt->close();
    }
    if ($conn instanceof mysqli) {
        $conn->close();
    }
}

session_write_close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado</title>
    <link rel="stylesheet" href="style/sectionP.css">
    <link rel="stylesheet" href="style/barraNav.css">
    <link rel="stylesheet" href="style/resultadoBusqueda.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <h3 id="url-impresion">URL: https://buscasa.gt.tc/resultadoBusqueda.php</h3>

    <ul id="menu-principal">
        <li><a href="<?= $enlaceInicio ?>" id="rgr"><span>Menú principal</span></a></li>
        <li><a href="busqueda.php" id="rgr"><span><i class="fa-solid fa-magnifying-glass"></i> Búsqueda</span></a></li>
        <?php if (!isset($_SESSION["usuario"])): ?>
            <li><a href="signUp.php" id="lgn"><span>Registrarse</span></a></li>
            <li><a href="partePrivada.php" id="rgr"><span>Perfil</span></a></li>
        <?php else: ?>
            <li><a href="partePrivada.php" id="rgr"><span>Perfil (<?= obtenerSaludo() ?>)</span></a></li>
            <li><a href="resultadoBusqueda.php?salir=1" id="lgn"><span>Salir</span></a></li>
        <?php endif; ?>
    </ul>

    <?php mostrarBarraEstilos(); ?>

    <input type="checkbox" id="print-toggle" style="display: none;">
    <label for="print-toggle" style="display: block; margin-bottom: 10px; color: black; cursor: pointer; clear: both;">
        <u>Cambiar vista normal/impresión</u>
    </label>

    <h2>Resultados de la búsqueda</h2>
    <hr size="1px" align="left" color="black">
    <br><br>

    <?php if ($errorBD !== ""): ?>
        <p style="color: red; font-weight: bold;"><?= htmlspecialchars($errorBD, ENT_QUOTES, 'UTF-8') ?></p>
    <?php elseif (!empty($errores)): ?>
        <div style="border: 2px solid red; padding: 12px; background-color: #ffe6e6; margin-bottom: 15px;">
            <p style="color: red; font-weight: bold;">Los criterios de búsqueda introducidos no son válidos:</p>
            <ul>
                <?php foreach ($errores as $err): ?>
                    <li style="color: red;"><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php elseif (count($resultados) > 0): ?>
        <?php foreach ($resultados as $anuncio): ?>
            <section>
                <a href="anuncio.php?id=<?= (int)$anuncio['IdAnuncio'] ?>">
                    <img src="<?= htmlspecialchars((string)($anuncio['FPrincipal'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars((string)($anuncio['Alternativo'] ?? $anuncio['Titulo']), ENT_QUOTES, 'UTF-8') ?>">
                </a>
                <p><strong><?= htmlspecialchars((string)$anuncio['Titulo'], ENT_QUOTES, 'UTF-8') ?></strong></p>
                <p><?= htmlspecialchars((string)$anuncio['TipoAnuncio'], ENT_QUOTES, 'UTF-8') ?> - <?= htmlspecialchars((string)$anuncio['TipoVivienda'], ENT_QUOTES, 'UTF-8') ?></p>
                <p><?= htmlspecialchars(date('d/m/Y', strtotime((string)$anuncio['FRegistro'])), ENT_QUOTES, 'UTF-8') ?></p>
                <p><?= htmlspecialchars((string)($anuncio['Ciudad'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                <p><?= htmlspecialchars((string)($anuncio['Pais'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                <p><?= htmlspecialchars(number_format((float)$anuncio['Precio'], 2, ',', '.'), ENT_QUOTES, 'UTF-8') ?> €</p>
            </section>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No se han encontrado anuncios con los criterios indicados.</p>
    <?php endif; ?>

    <?php mostrarPanelUltimosAnuncios($catalogoAnuncios); ?>

    <footer>
        <br><br>
        <hr size="1px" align="center" color="black">
        <p>© 2026. Aviso de copyright: Contenido de uso libre.</p>
    </footer>
</body>
</html>