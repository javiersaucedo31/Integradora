function openModal() {
    document.getElementById("perfil").style.display = "block";
}

function closeModal() {
        document.getElementById("perfil").style.display = "none";
    }

window.onclick = function(event) {
        var modal = document.getElementById("perfil");
        if (event.target == modal) {
            modal.style.display = "none";
        }
     }


function openModal2() {
    document.getElementById("movements").style.display = "block";
}


function closeModal2() {
        document.getElementById("movements").style.display = "none";
    }


    window.onclick = function(event) {
        var modal = document.getElementById("movements");
        if (event.target == modal) {
            modal.style.display = "none";
        }
     }     


     function Modaloperacion() {
        document.getElementById("realizar-operacion").style.display = "block";
    }
    
        function closeoperacion() {
            document.getElementById("realizar-operacion").style.display = "none";
        }
    
        window.onclick = function(event) {
            var modal = document.getElementById("realizar-operacion");
            if (event.target == modal) {
                modal.style.display = "none";
            }
         } 
        
         
         function mostrarFormulario(formularioId) {
            // Ocultar ambos formularios
            document.getElementById('guardar_contacto').style.display = 'none';
            document.getElementById('operation_form').style.display = 'none';
        
            // Mostrar el formulario seleccionado
            document.getElementById(formularioId).style.display = 'block';
        } 

        function llenarCuentaDestino(selectElement) {
            document.getElementById('cuenta-destino').value = selectElement.value;
        }
         

         