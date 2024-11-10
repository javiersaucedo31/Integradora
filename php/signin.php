<?php
// Habilitar la visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir el archivo de conexión
include 'conextion.php';

// Verificar si la solicitud es POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener y sanitizar los datos del formulario
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Verificar si los campos no están vacíos
    if (empty($username) || empty($password)) {
        header("Location: /integradora/signin.html?mensaje=" . urlencode("Todos los campos son obligatorios."));
        exit();
    }

    // Preparar la consulta para verificar las credenciales
    $sql = "SELECT u.id_usuario, u.password, p.nombre FROM usuario u 
            JOIN persona p ON u.id_persona = p.id_persona 
            WHERE u.username = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        header("Location: /integradora/signin.html?mensaje=" . urlencode("Error en la base de datos: " . $conn->error));
        exit();
    }

    // Bind de parámetros y ejecución
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si existe el usuario
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];
        $nombre = $row['nombre'];

        // Verificar la contraseña ingresada con la almacenada
        if (password_verify($password, $hashed_password)) {
            // Inicio de sesión exitoso
            session_start();
            $_SESSION['username'] = $username;
            $_SESSION['nombre'] = $nombre;
            $_SESSION['id_usuario'] = $id_usuario; // Almacenar el ID del usuario

            // Redirigir al usuario al área principal
            header("Location: /integradora/pagina_main_html.php");
            exit();
        } else {
            // Contraseña incorrecta
            header("Location: /integradora/signin.html?mensaje=" . urlencode("Contraseña incorrecta."));
            exit();
        }
    } else {
        // Usuario no encontrado
        header("Location: /integradora/signin.html?mensaje=" . urlencode("El usuario no está registrado."));
        exit();
    }

    // Cerrar la declaración
    $stmt->close();
}

// Cerrar la conexión
$conn->close();
?>
