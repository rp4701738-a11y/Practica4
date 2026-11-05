'use strict';

function mostrarErr(idEntrada, idError, mensaje) {
    const entrada = document.getElementById(idEntrada);
    const spanError = document.getElementById(idError);
    
    entrada.classList.add("input-error");
    spanError.innerHTML = mensaje;
    spanError.style.color = "rgb(255, 55, 0)"; 
    spanError.style.display = "inline";
}

function limpiarErr() {
    const spansError = document.querySelectorAll(".error-msg");
    const entradasError = document.querySelectorAll(".input-error");
    for (let i = 0; i < spansError.length; i++) {
        spansError[i].style.display = "none";
        spansError[i].innerHTML = "";
    }
    for (let i = 0; i < entradasError.length; i++) {
        entradasError[i].classList.remove("input-error");
    }
}

function esAlfanum(caracter) {
    const codigo = caracter.charCodeAt(0);
    return (codigo >= 48 && codigo <= 57) || 
           (codigo >= 65 && codigo <= 90) || 
           (codigo >= 97 && codigo <= 122);  
}

function esNum(caracter) {
    const codigo = caracter.charCodeAt(0);
    return (codigo >= 48 && codigo <= 57);
}

function validarNomUsu(nomUsu) {
    if (nomUsu.length < 3 || nomUsu.length > 15) return false;
    if (esNum(nomUsu.charAt(0))) return false;
    for (let i = 0; i < nomUsu.length; i++) {
        if (!esAlfanum(nomUsu.charAt(i))) return false;
    }
    return true;
}

function validarPassw(passw) {
    if (passw.length < 6 || passw.length > 15) return false;
    let tieneMayus = false;
    let tieneMinus = false;
    let tieneNum = false;

    for (let i = 0; i < passw.length; i++) {
        const caracter = passw.charAt(i);
        if (caracter >= 'A' && caracter <= 'Z') tieneMayus = true;
        else if (caracter >= 'a' && caracter <= 'z') tieneMinus = true;
        else if (caracter >= '0' && caracter <= '9') tieneNum = true;
        else if (caracter !== '-' && caracter !== '_') return false; 
    }
    return tieneMayus && tieneMinus && tieneNum;
}

function validarCorreo(correo) {
    if (correo.length > 254) return false;
    const partes = correo.split('@');
    if (partes.length !== 2) return false;
    
    const local = partes[0];
    const dominio = partes[1];
    if (local.length < 1 || local.length > 64) return false;
    if (dominio.length < 1 || dominio.length > 255) return false;

    if (local.charAt(0) === '.' || local.charAt(local.length - 1) === '.') return false;
    if (local.includes('..')) return false;

    const subdominios = dominio.split('.');
    if (subdominios.length < 2) return false;
    for (let i = 0; i < subdominios.length; i++) {
        const sub = subdominios[i];
        if (sub.length < 1 || sub.length > 63) return false;
        if (sub.charAt(0) === '-' || sub.charAt(sub.length - 1) === '-') return false;
    }
    return true;
}

function validarEdad(fecha) {
    const fechaNac = new Date(fecha);
    if (isNaN(fechaNac.getTime())) return false; 
    
    const hoy = new Date();
    let edad = hoy.getFullYear() - fechaNac.getFullYear();
    const meses = hoy.getMonth() - fechaNac.getMonth();
    if (meses < 0 || (meses === 0 && hoy.getDate() < fechaNac.getDate())) {
        edad--;
    }
    return edad >= 18;
}

function validarForm(evento) {
    evento.preventDefault(); 

    limpiarErr();
    let esValido = true;

    const nomUsu = document.getElementById("username").value.trim();
    const passw = document.getElementById("password").value.trim();
    const repetirpassw = document.getElementById("repeatPassword").value.trim();
    const correo = document.getElementById("email").value.trim();
    const sexo = document.getElementById("sexo").value.trim();
    const fecha = document.getElementById("fechaNacimiento").value.trim();

    if (nomUsu === '') {
        mostrarErr("username", "err-username", "Campo obligatorio");
        esValido = false;
    } else if (!validarNomUsu(nomUsu)) {
        mostrarErr("username", "err-username", "Debe tener entre 3 y 15 caracteres y no empezar por número.");
        esValido = false;
    }

    if (passw === '') {
        mostrarErr("password", "err-password", "Campo obligatorio");
        esValido = false;
    } else if (!validarPassw(passw)) {
        mostrarErr("password", "err-password", "Debe tener 6-15 caracteres, 1 mayúscula, 1 minúscula y 1 número.");
        esValido = false;
    }

    if (repetirpassw === '') {
        mostrarErr("repeatPassword", "err-repeatPassword", "Campo obligatorio");
        esValido = false;
    } else if (passw !== repetirpassw) {
        mostrarErr("repeatPassword", "err-repeatPassword", "Las contraseñas no coinciden.");
        esValido = false;
    }

    if (correo === '') {
        mostrarErr("email", "err-email", "Campo obligatorio");
        esValido = false;
    } else if (!validarCorreo(correo)) {
        mostrarErr("email", "err-email", "Formato de correo no válido.");
        esValido = false;
    }

    if (sexo === '') {
        mostrarErr("sexo", "err-sexo", "Campo obligatorio");
        esValido = false;
    }

    if (fecha === '') {
        mostrarErr("fechaNacimiento", "err-fechaNacimiento", "Campo obligatorio");
        esValido = false;
    } else if (!validarEdad(fecha)) {
        mostrarErr("fechaNacimiento", "err-fechaNacimiento", "Debe tener al menos 18 años (Formato: YYYY-MM-DD).");
        esValido = false;
    }

    if (esValido === true) {
        evento.target.submit();
    }
}

document.addEventListener("DOMContentLoaded", function() {
    const formulario = document.getElementById("signupForm");
    if (formulario) {
        formulario.addEventListener("submit", validarForm);
    }
});