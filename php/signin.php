<?php
 session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include 'conextion.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
        header("Location: /integradora/signin_html.php");
        exit();
    }

    
    $sql = "SELECT u.id_usuario, u.id_rol, u.password, p.nombre FROM usuario u 
            JOIN persona p ON u.id_persona = p.id_persona 
            WHERE u.username = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $_SESSION['error'] = "Error en la base de datos.";
        header("Location: /integradora/signin_html.php?mensaje=" . urlencode("Error en la base de datos: " . $conn->error));
        exit();
    }

    
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

   
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];
        $nombre = $row['nombre'];
        $id_rol = $row['id_rol'];

        
        if (password_verify($password, $hashed_password)) {
           
            $_SESSION['username'] = $username;
            $_SESSION['nombre'] = $nombre;
            $_SESSION['id_usuario'] = $id_usuario; 
            $_SESSION['id_rol'] = $id_rol;

            if($id_rol==2){
                header("location: /integradora/admin_dashboard.php");
            }
            else{
            header("Location: /integradora/pagina_main_html.php");
            }
            exit();
        } else {
            $_SESSION['error'] = "Contraseña incorrecta";
            header("Location: /integradora/signin_html.php" );
            exit();
        }
    } else {
        $_SESSION['error'] = "Usuarion no encontrado";
        header("Location: /integradora/signin_html.php");
        exit();
    }

    
    $stmt->close();
}

$conn->close();
?>
