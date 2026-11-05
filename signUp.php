<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
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
    <h3 id="url-impresion">URL: https://buscasa.gt.tc/signUp.html</h3>

    <nav>
        <ul>
            <li><a href="index.php" id="rgr"><span>Menú principal</span></a></li>
            <li><a href="busqueda.php" id="rgr"><span><i class="fa-solid fa-magnifying-glass"></i> Búsqueda</span></a></li>   <!--Este boton tiene icono-->
            <li><a href="partePrivada.php" id="rgr"><span>Perfil</span></a></li>
        </ul>
    </nav>
    <nav>
        <ul>
            <li><span for="osc" id="lol">Modo oscuro:<input type="checkbox" id="osc"></span></li>
            <li><span for="letr" id="lol">Letra grande:<input type="checkbox" id="letr"></span></li>
            <li><span for="cont" id="lol">Contraste:<input type="checkbox" id="cont"></span></li>
            <li><span for="cont" id="lol">Ambos:<input type="checkbox" id="amb"></span></li>
        </ul>
    </nav>

    <input type="checkbox" id="print-toggle" style="display: none;">
    <label for="print-toggle" style="display: block; margin-bottom: 10px; color: black; cursor: pointer; clear: both;">
        <u>Cambiar vista normal/impresión</u>
    </label>

    <section id="signIn">
        <h1>Registro:</h1>
        <form id="signupForm" action="index2.php">
            <!-- Cada input recibe un 'id' que coincida con el JavaScript -->
            <label>Nombre de usuario: <input type="text" id="username" name="username"></label>
            <span id="err-username" class="error-msg"></span>
            
            <br><br><label>Contraseña: <input type="password" id="password" name="password"></label>
            <span id="err-password" class="error-msg"></span>
            
            <br><br><label>Repita la contraseña: <input type="password" id="repeatPassword" name="repeatPassword"></label>
            <span id="err-repeatPassword" class="error-msg"></span>
            
            <br><br><label>Email: <input type="text" id="email" name="email"></label>
            <span id="err-email" class="error-msg"></span>
            
            <br><br><label>Sexo: <input type="text" id="sexo" name="sexo"></label>
            <span id="err-sexo" class="error-msg"></span>
            
            <br><br><label>Fecha de nacimiento: <input type="text" id="fechaNacimiento" name="fechaNacimiento"></label>
            <span id="err-fechaNacimiento" class="error-msg"></span>
            
            <br><br><label>Ciudad de residencia: <input type="text" id="ciudad" name="ciudad"></label>
            <br><br><label>País de residencia: <input type="text" id="pais" name="pais"></label>
            <br><br><label id="foto">Foto: <input type="file" id="fotoFile" name="fotoFile"></label>
            
            <br><br><input type="submit" value="Enviar">
        </form>
        <p>Ya tienes una cuenta? <a href="signIn.php">Inicia sesión</a></p>
    </section>

    <footer>
        <br><br>
        <hr size="1px" align="center" color="black">
        <p>&copy; 2026. Aviso de copyright: Contenido de uso libre.</p>
    </footer>
</body>
</html>