'use strict';
var forma = document.getElementById('tb');
forma.style.display = "none";
function mostrar(){
if (forma.style.display === "none") {
    forma.style.display = "block";
}else{
    forma.style.display = "none";
}
}

function crear(){
    var tabla = document.getElementById('ta');
    var m = 12;
    var n = 12.6;
    var j = 13.5;
    var p = 14.1;
    tabla.innerHTML = "<tr> <td></td> <td></td> <td colspan='2'>Blanco y negro</td> <td colspan='2'>Color</td></tr>"
    tabla.innerHTML += "<tr> <td>N de <br>paginas</td> <td>N de <br>fotos</td> <td>150-300 dpi</td> <td>450-900 dpi</td>  <td>150-300 dpi</td> <td>450-900 dpi</td></tr>"
    for (let i = 0; i < 15; i++) {
        tabla.innerHTML += "<tr> <td>"+(i+1)+"</td> <td>"+((i+1)*3)+"</td> <td>"+cal(m , i)+"€ </td> <td>"+cal1(n , i)+"€</td> <td>"+cal2(j , i)+"€</td> <td>"+cal3(p , i)+"€</td></tr>"
        m = cal(m , i);   
        n = cal1(n , i); 
        j = cal2(j , i);
        p = cal3(p , i);
    }
}
crear();

function cal( m , i){
let n = 2 , q = 1.8 , p = 1.6;
if (i === 0) {
    return m;
}
if (i < 4) {
    m += n;
}else if(i < 10){
    m += q;
}else{
    m += p;
}

return Math.round(m * 100) / 100;
}

function cal1( m , i){
let n = 2.6 , q = 2.4 , p = 2.2;
if (i === 0) {
    return m;
}
if (i < 4) {
    m += n;
}else if(i < 10){
    m += q;
}else{
    m += p;
}

return Math.round(m * 100) / 100;
}

function cal2( m , i){
let n = 3.5 , q = 3.3 , p = 3.1;
if (i === 0) {
    return m;
}
if (i < 4) {
    m += n;
}else if(i < 10){
    m += q;
}else{
    m += p;
}

return Math.round(m * 100) / 100;
}

function cal3( m , i){
let n = 4.1 , q = 3.9 , p = 3.7;
if (i === 0) {
    return m;
}
if (i < 4) {
    m += n;
}else if(i < 10){
    m += q;
}else{
    m += p;
}

return Math.round(m * 100) / 100;
}

