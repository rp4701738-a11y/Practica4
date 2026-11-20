<?php
$actionEsc = htmlspecialchars((string)$actionFormulario, ENT_QUOTES, 'UTF-8');
$botonEsc = htmlspecialchars((string)$textoBoton, ENT_QUOTES, 'UTF-8');
?>
<form id="signupForm" action="<?= $actionEsc ?>" method="post" novalidate>
    <label>Nombre de usuario: <input type="text" id="username" name="username" value="<?= htmlspecialchars((string)($datosFormulario['username'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></label>
    <span id="err-username" class="error-msg"></span>

    <br><br><label>Contraseña: <input type="password" id="password" name="password"></label>
    <span id="err-password" class="error-msg"></span>

    <br><br><label>Repita la contraseña: <input type="password" id="repeatPassword" name="repeatPassword"></label>
    <span id="err-repeatPassword" class="error-msg"></span>

    <br><br><label>Email: <input type="text" id="email" name="email" value="<?= htmlspecialchars((string)($datosFormulario['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></label>
    <span id="err-email" class="error-msg"></span>

    <br><br><label>Sexo: <input type="text" id="sexo" name="sexo" value="<?= htmlspecialchars((string)($datosFormulario['sexo'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></label>
    <span id="err-sexo" class="error-msg"></span>

    <br><br><label>Fecha de nacimiento: <input type="text" id="fechaNacimiento" name="fechaNacimiento" value="<?= htmlspecialchars((string)($datosFormulario['fechaNacimiento'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></label>
    <span id="err-fechaNacimiento" class="error-msg"></span>

    <br><br><label>Ciudad de residencia: <input type="text" id="ciudad" name="ciudad" value="<?= htmlspecialchars((string)($datosFormulario['ciudad'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></label>
    <br><br><label>País de residencia: <?php renderSelectPaises($paises, $datosFormulario['pais'] ?? '', '-- Selecciona un país --', 'pais', 'pais'); ?></label>
    <?php if (!empty($datosFormulario['fotoActual'])): ?>
        <br><br><p>Foto actual:</p>
        <img src="<?= htmlspecialchars((string)$datosFormulario['fotoActual'], ENT_QUOTES, 'UTF-8') ?>" alt="Foto de perfil actual" style="width: 120px; height: 120px; object-fit: cover;">
    <?php endif; ?>
    <br><br><label id="foto">Foto: <input type="file" id="fotoFile" name="fotoFile"></label>

    <br><br><input type="submit" value="<?= $botonEsc ?>">
</form>