<?php
include 'conextion.php';
session_start();

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['username'])) {
    $_SESSION['errore'] = "Inicia sesion primero.";
    header("Location: /integradora/signin_html.php");
    exit();
}

// Obtener el número de cuenta del usuario a partir del username de la sesión
$username = $_SESSION['username'];
$sql = "SELECT c.id_usuario, c.numero_cuenta FROM usuario u 
        JOIN cuentas c ON u.id_usuario = c.id_usuario 
        WHERE u.username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc();
    $id_usuario = $user_data['id_usuario'];
} else {
    $_SESSION['errore'] = "Usuario no encontrado";
    header("Location: /integradora/signin_html.php");
    exit();
}

// Recoger los datos del formulario
$nombre_contacto = $_POST['nombre-contacto'];
$cuenta_destino = $_POST['cuenta-destino-guardar'];

// Paso 1: Verificar si la cuenta de destino existe en la tabla de cuentas
$stmt = $conn->prepare("SELECT id_cuenta FROM cuentas WHERE numero_cuenta = ?");
$stmt->bind_param("s", $cuenta_destino);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['errore'] = "Cuenta no encontrada";
    header("Location: /integradora/pagina_main_html.php");
    exit();
}

// Paso 2: Guardar el contacto si la cuenta de destino existe
$stmt = $conn->prepare("INSERT INTO contactos (id_usuario, nombre_contacto, numero_cuenta) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $id_usuario, $nombre_contacto, $cuenta_destino);

if ($stmt->execute()) {
    $_SESSION['success'] = "Contacto guardado exitosamente";
    header("Location: /integradora/pagina_main_html.php");
} else {
    $_SESSION['errore'] = "Error al guardar contacto";
    header("Location: /integradora/pagina_main_html.php?mensaje=" . urlencode("Error al guardar el contacto."));
}

$stmt->close();
$conn->close();
?>
