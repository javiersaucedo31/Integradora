function loadView(view) {
    const mainContent = document.getElementById('main-content');

   
    fetch(view)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(data => {
            mainContent.innerHTML = data; 
        })
        .catch(error => {
            mainContent.innerHTML = `<p>Error al cargar la vista: ${error.message}</p>`;
        });
}


function openModal() {
    document.getElementById("aboutUsModal").style.display = "block";
    }

    function closeModal() {
        document.getElementById("aboutUsModal").style.display = "none";
    }

    window.onclick = function(event) {
        var modal = document.getElementById("aboutUsModal");
        if (event.target == modal) {
            modal.style.display = "none";
        }
     }




 function showLoginForm() {
    const loginForm = document.getElementById('form-login');
    loginForm.classList.add('show'); 
}


document.addEventListener("DOMContentLoaded", function() {
    showLoginForm();
});       


function prevenirsubmitdef(e){
    e.preventDefault();

    console.log("hola");
};

const hoy = new Date().toISOString().split('T')[0];
        document.getElementById("fecha_nac").setAttribute("max", hoy);

        function calcularEdad(fechaNacimiento) {
            const hoy = new Date();
            const nacimiento = new Date(fechaNacimiento);
            let edad = hoy.getFullYear() - nacimiento.getFullYear();
            const mes = hoy.getMonth() - nacimiento.getMonth();
            if (mes < 0 || (mes === 0 && hoy.getDate() < nacimiento.getDate())) {
                edad--;
            }
            return edad;
        }

        // Validación cuando se intenta enviar el formulario
        document.getElementById('registroForm').addEventListener('submit', function(event) {
            const fechaNacimiento = document.getElementById('fecha_nac').value;
            const edad = calcularEdad(fechaNacimiento);

            if (edad < 18) {
                event.preventDefault();  // Evita que el formulario se envíe
                alert("Debes tener al menos 18 años para registrarte.");
            } else {
                alert("Formulario enviado con éxito");
            }
        });


        function toggleMenu() {
            const navLinks = document.getElementById('navLinks');
            navLinks.classList.toggle('show');
        }     
   
