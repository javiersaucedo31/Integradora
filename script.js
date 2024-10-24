function loadView(view) {
    const mainContent = document.getElementById('main-content');

    // Usar fetch para cargar el contenido del archivo HTML
    fetch(view)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(data => {
            mainContent.innerHTML = data; // Cargar el contenido en el main-content
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
    loginForm.classList.add('show'); // Añade la clase 'show' para desplegar el formulario
}

// Llamamos a la función cuando cargue la página
document.addEventListener("DOMContentLoaded", function() {
    showLoginForm();
});       


function prevenirsubmitdef(e){
    e.preventDefault();
}


   
