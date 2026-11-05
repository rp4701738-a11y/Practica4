'use strict';

function mostrarErrLogin(idEntrada, idError, mensaje) {
    const entrada = document.getElementById(idEntrada);
    const spanErr = document.getElementById(idError);
    
    entrada.classList.add("input-error");
    spanErr.innerHTML = mensaje;
    spanErr.style.color = "rgb(255, 55, 0)"; 
    spanErr.style.display = "inline";
}

function limpiarErrLogin() {
    const spansErr = document.querySelectorAll(".error-msg");
    const entradasErr = document.querySelectorAll(".input-error");
    for (let i = 0; i < spansErr.length; i++) {
        spansErr[i].style.display = "none";
        spansErr[i].innerHTML = "";
    }
    for (let i = 0; i < entradasErr.length; i++) {
        entradasErr[i].classList.remove("input-error");
    }
}

function validarLogin(evento) {
    evento.preventDefault(); 
    
    limpiarErrLogin();
    let esValido = true;
    
    const usu = document.getElementById("loginUser").value.trim();
    const passw = document.getElementById("loginPass").value.trim();
    
    if (usu === "") {
        mostrarErrLogin("loginUser", "err-loginUser", "El nombre de usuario es obligatorio.");
        esValido = false;
    }
    
    if (passw === "") {
        mostrarErrLogin("loginPass", "err-loginPass", "La contraseña es obligatoria.");
        esValido = false;
    }
    
    if (esValido === true) {
        evento.target.submit();
    }
}

document.addEventListener("DOMContentLoaded", function() {
    const formulario = document.getElementById("loginForm");
    if (formulario) {
        formulario.addEventListener("submit", validarLogin);
    }
});