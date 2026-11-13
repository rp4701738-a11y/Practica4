<?php
date_default_timezone_set('Europe/Madrid');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuarios = [
    [
        "usuario" => "usuario1",
        "contrasena" => "1234",
        "nombre" => "Usuario Uno",
        "token" => "tok_u1_98f2a1b4c3",
        "estilo" => "osc" // Modo oscuro
    ],
    [
        "usuario" => "usuario2",
        "contrasena" => "5678",
        "nombre" => "Usuario Dos",
        "token" => "tok_u2_77d4e9a2f1",
        "estilo" => "letr" // Letra grande
    ],
    [
        "usuario" => "usuario3",
        "contrasena" => "abcd",
        "nombre" => "Usuario Tres",
        "token" => "tok_u3_55c1b8e3d9",
        "estilo" => "cont" // Alto contraste
    ],
    [
        "usuario" => "usuario4",
        "contrasena" => "efgh",
        "nombre" => "Usuario Cuatro",
        "token" => "tok_u4_33a7f6c2b8",
        "estilo" => "amb" // Ambos (contraste + letra grande)
    ]
];

$catalogoAnuncios = [
    1 => [
        "id" => 1,
        "tipo_anuncio" => "Venta",
        "tipo_vivienda" => "Casa",
        "titulo" => "Casa en el Caribe",
        "fecha" => "20 de diciembre de 2025",
        "ciudad" => "Caribe",
        "pais" => "España",
        "precio" => "20000€",
        "imagen" => "imagenes/casa1.jpeg",
        "descripcion" => "Espectacular casa soleada junto a la costa."
    ],
    2 => [
        "id" => 2,
        "tipo_anuncio" => "Alquiler",
        "tipo_vivienda" => "Apartamento",
        "titulo" => "Apartamento en Murcia",
        "fecha" => "28 de diciembre de 2024",
        "ciudad" => "Murcia",
        "pais" => "España",
        "precio" => "15000€",
        "imagen" => "imagenes/apartamento1.jpeg",
        "descripcion" => "Apartamento céntrico totalmente equipado."
    ],
    3 => [
        "id" => 3,
        "tipo_anuncio" => "Alquiler",
        "tipo_vivienda" => "Casa",
        "titulo" => "Casa en la playa",
        "fecha" => "10 de enero de 2026",
        "ciudad" => "Castilla",
        "pais" => "España",
        "precio" => "20000€",
        "imagen" => "imagenes/casa1.jpeg",
        "descripcion" => "Casa mediana con vistas al mar, a las afueras de la ciudad."
    ],
    4 => [
        "id" => 4,
        "tipo_anuncio" => "Venta",
        "tipo_vivienda" => "Apartamento",
        "titulo" => "Apartamento en la montaña",
        "fecha" => "15 de febrero de 2026",
        "ciudad" => "Madrid",
        "pais" => "España",
        "precio" => "35000€",
        "imagen" => "imagenes/apartamento1.jpeg",
        "descripcion" => "Tranquilo apartamento rodeado de naturaleza."
    ],
    5 => [
        "id" => 5,
        "tipo_anuncio" => "Venta",
        "tipo_vivienda" => "Casa rural",
        "titulo" => "Casa rural",
        "fecha" => "05 de marzo de 2026",
        "ciudad" => "Valencia",
        "pais" => "España",
        "precio" => "15000€",
        "imagen" => "imagenes/casa1.jpeg",
        "descripcion" => "Acogedora casa rural ideal para desconectar."
    ]
];

// Procesar cierre de sesión con botones Salir
if (isset($_POST['salir']) || isset($_GET['salir'])) {$_SESSION = array();
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 42000, '/', '', false, true);
    }
    session_destroy();

    // Borrar cookies de "Recordarme" y de "Últimos anuncios visitados"
    setcookie('recordar_token', '', time() - 3600, '/', '', false, true);
    setcookie('recordar_visita', '', time() - 3600, '/', '', false, true);
    setcookie('recordar_expira', '', time() - 3600, '/', '', false, true);
    setcookie('ultimos_anuncios', '', time() - 3600, '/', '', false, true);

    header("Location: index.php");
    exit();
}

// Recuperación automática de sesión si no hay sesión activa pero existe la cookie "Recordarme"
if (!isset($_SESSION["usuario"]) && isset($_COOKIE["recordar_token"])) {
    $tokenCookie =$_COOKIE["recordar_token"];
    foreach ($usuarios as$u) {
        if ($u["token"] === $tokenCookie) {
            $_SESSION["usuario"] = $u["usuario"];
            $_SESSION["nombre"] = $u["nombre"];
            $_SESSION["estilo"] = $u["estilo"];
            $_SESSION["recordado"] = true;

            // La última visita que se muestra es la almacenada previamente en la cookie
            $visitaAnterior =$_COOKIE["recordar_visita"] ?? date("d/m/y \a \l\a\s H:i");
            $_SESSION["ultima_visita"] = $visitaAnterior;

            // Actualizamos la fecha de última visita para la próxima vez que se recupere por cookie,
            // conservando exactamente la fecha de caducidad original (sin sliding expiration)
            $expiracionOriginal = isset($_COOKIE["recordar_expira"])
                ? (int)$_COOKIE["recordar_expira"]
                : (time() + 90 * 24 * 60 * 60);

            if ($expiracionOriginal > time()) {$ahora = date("d/m/y \a \l\a\s H:i");
                setcookie('recordar_visita', $ahora,$expiracionOriginal, '/', '', false, true);
            }
            break;
        }
    }
}

// Función para proteger páginas privadas y públicas restringidas
function protegerRuta() {
    if (!isset($_SESSION["usuario"])) {
        $_SESSION["flash"]["error"] = "Debes iniciar sesión para acceder a esa página.";
        session_write_close();
        header("Location: index.php");
        exit();
    }
}

// Función para obtener y borrar un mensaje Flashdata
function obtenerFlash($clave) {
    if (isset($_SESSION["flash"][$clave])) {$valor = $_SESSION["flash"][$clave];
        unset($_SESSION["flash"][$clave]);
        return $valor;
    }
    return null;
}

// Función para calcular el saludo según la franja horaria
function obtenerSaludo() {
    $usuario =$_SESSION["usuario"] ?? "Usuario";
    $hora = (int) date('H');
    if ($hora >= 6 && $hora < 12) {$saludo = "Buenos días";
    } elseif ($hora >= 12 && $hora < 16) {$saludo = "Hola";
    } elseif ($hora >= 16 && $hora < 20) {$saludo = "Buenas tardes";
    } else {
        $saludo = "Buenas noches";
    }
    return $saludo . ", " . htmlspecialchars($usuario);
}

// Función para marcar como checked el estilo asignado al usuario en sesión
function atributoEstilo($idCheckbox) {
    $estiloActual =$_SESSION["estilo"] ?? "";
    return ($estiloActual ===$idCheckbox) ? "checked" : "";
}

// Función para registrar un anuncio visitado en la cookie (duración: 1 semana, máx. 4 anuncios)
function registrarVisitaAnuncio($idAnuncio,$catalogo) {
    if (!isset($catalogo[$idAnuncio])) { return []; }$lista = [];
    if (isset($_COOKIE['ultimos_anuncios'])) {
        $decodificado = json_decode($_COOKIE['ultimos_anuncios'], true);
        if (is_array($decodificado)) {
            $lista = array_map('intval',$decodificado);
        }
    }

    // Si ya estaba visitado, lo quitamos de su posición anterior para no duplicarlo
    $indice = array_search((int)$idAnuncio,$lista, true);
    if ($indice !== false) {
        unset($lista[$indice]);
        $lista = array_values($lista);
    }

    // Añadimos el anuncio al final para respetar el orden de visita
    $lista[] = (int)$idAnuncio;

    // Si supera los 4 anuncios, eliminamos el primero (el más antiguo de los cuatro)
    if (count($lista) > 4) {
        array_shift($lista);
    }

    $caducidadSemana = time() + (7 * 24 * 60 * 60);
    setcookie('ultimos_anuncios', json_encode($lista),$caducidadSemana, '/', '', false, true);
    $_COOKIE['ultimos_anuncios'] = json_encode($lista);
    return $lista;
}

// Función para renderizar el panel "Últimos anuncios visitados"
function mostrarPanelUltimosAnuncios($catalogo) {$lista = [];
    if (isset($_COOKIE['ultimos_anuncios'])) {
        $decodificado = json_decode($_COOKIE['ultimos_anuncios'], true);
        if (is_array($decodificado)) {
            $lista = array_map('intval',$decodificado);
        }
    }

    echo '<aside style="clear: both; padding-top: 25px; margin-top: 20px;">';
    echo '<h2>Últimos anuncios visitados</h2>';
    echo '<hr size="1px" align="left" color="black">';

    if (empty($lista)) {
        echo '<p>Aún no has visitado ningún anuncio recientemente.</p>';
    } else {
        echo '<div style="display: flex; flex-wrap: wrap; gap: 15px;">';
        foreach ($lista as$id) {
            if (isset($catalogo[$id])) {$an = $catalogo[$id];
                echo '<section style="float: none; position: static; height: auto; width: 220px; margin: 5px; padding: 10px;">';
                echo '<a href="anuncio.php?id=' . (int)$an['id'] . '">';
                echo '<img src="' . htmlspecialchars($an['imagen']) . '" alt="' . htmlspecialchars($an['titulo']) . '" style="width: 100%; height: 130px; object-fit: cover;">';
                echo '<p><strong>' . htmlspecialchars($an['titulo']) . '</strong></p>';
                echo '</a>';
                echo '<p>Ciudad: ' . htmlspecialchars($an['ciudad']) . '</p>';
                echo '<p>País: ' . htmlspecialchars($an['pais']) . '</p>';
                echo '<p>Precio: ' . htmlspecialchars($an['precio']) . '</p>';
                echo '</section>';
            }
        }
        echo '</div>';
    }
    echo '</aside>';
}

// Función para renderizar la barra de estilos conservando la selección en sesión
function mostrarBarraEstilos() {
    ?>
    <nav>
        <ul>
            <li><span id="lol">Modo oscuro:<input type="checkbox" id="osc" <?= atributoEstilo('osc') ?>></span></li>
            <li><span id="lol">Letra grande:<input type="checkbox" id="letr" <?= atributoEstilo('letr') ?>></span></li>
            <li><span id="lol">Contraste:<input type="checkbox" id="cont" <?= atributoEstilo('cont') ?>></span></li>
            <li><span id="lol">Ambos:<input type="checkbox" id="amb" <?= atributoEstilo('amb') ?>></span></li>
        </ul>
    </nav>
    <?php
}
?>