<?php
session_start();
session_destroy(); // Destruye la sesión
header("Location: /integradora/pagina_inicio.html?mensaje=" . urlencode("Has cerrado sesión exitosamente."));
exit();
?>