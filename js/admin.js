function openModal_admin() {
    document.getElementById("perfil-admin").style.display = "block";
}

function closeModal_admin() {
        document.getElementById("perfil-admin").style.display = "none";
    }

window.onclick = function(event) {
        var modal = document.getElementById("perfil-admin");
        if (event.target == modal) {
            modal.style.display = "none";
        }
     }
