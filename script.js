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




 


        function toggleMenu() {
            const navLinks = document.getElementById('navLinks');
            navLinks.classList.toggle('show');
        }     
   
