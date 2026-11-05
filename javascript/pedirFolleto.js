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
