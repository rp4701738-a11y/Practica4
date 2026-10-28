'use strict';
function cambio(){
var us = document.getElementById('nu').value.trim();
var co = document.getElementById('ct').value.trim();
var nim = document.getElementById('m');
var nur = document.getElementById('l');
nim.style.color = "rgb(255, 55, 0)";
nur.style.color = "rgb(255, 55, 0)";
if (co === '' && us === '') {

    nim.innerHTML = "Campo obligatorio";
    nur.innerHTML = "Campo obligatorio";
    

}else if (co=== '') {

    nim.innerHTML = "Campo obligatorio";

}else if(us === ''){

    nur.innerHTML = "Campo obligatorio";

}else{
    location.href = "./index2.html";
}
}

function nover( contr ){
        contr.value = "*".repeat(contr.value.length);
    
}