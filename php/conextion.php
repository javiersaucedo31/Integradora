
<?php
// Datos de conexión
$servername = "localhost";
$username = "root"; // Cambia esto si tu usuario es diferente
$password = ""; // Añade la contraseña si tienes una
$database = "integradora";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// No se debe imprimir nada aquí, solo verificar que la conexión sea exitosa
?>
