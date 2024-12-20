<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include 'conextion.php';


if ($conn->connect_error) {
    header("Location: /integradora/register_html.php?mensaje=" . urlencode("Error de conexión: " . $conn->connect_error));
    exit();
}

$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$correo = $_POST['correo'];
$telefono = $_POST['telefono'];
$fecha_nac = $_POST['fecha_nac'];
$username = $_POST['register-username'];
$password = $_POST['password-register'];


if (empty($nombre) || empty($apellido) || empty($correo) || empty($username) || empty($password) || empty($fecha_nac)) {
    $mensaje="todos los Campos son obligatorios";
    header("Location: /integradora/register_html.php?mensaje=" . urlencode("Todos los campos son obligatorios."));
    exit();
}


$fecha_actual = new DateTime();
$fecha_nacimiento = new DateTime($fecha_nac);
$edad = $fecha_actual->diff($fecha_nacimiento)->y;

if ($edad < 18) {
    $mensaje="Debes tener al menos 18 años para registrarte";
    header("Location: /integradora/register_html.php?mensaje=" . urlencode("Debes tener al menos 18 años para registrarte."));
    exit();
}


$sql_check = "SELECT COUNT(*) as count FROM usuario WHERE username = ? OR id_persona IN (SELECT id_persona FROM persona WHERE email = ?)";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ss", $username, $correo);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
$row_check = $result_check->fetch_assoc();
if ($row_check['count'] > 0) {
    $mensaje="El usuario o correo ya estan registrados";
    header("Location: /integradora/register_html.php");
    exit();
}
$stmt_check->close();

$hashed_password = password_hash($password, PASSWORD_BCRYPT);


$conn->begin_transaction();
try {
    $sql_persona = "INSERT INTO persona (nombre, apellido, fecha_nac, email, telefono) VALUES (?, ?, ?, ?, ?)";
    $stmt_persona = $conn->prepare($sql_persona);
    if (!$stmt_persona) {
        throw new Exception("Error en la preparación de la consulta persona: " . $conn->error);
    }
    $stmt_persona->bind_param("sssss", $nombre, $apellido, $fecha_nac, $correo, $telefono);
    if (!$stmt_persona->execute()) {
        throw new Exception("Error al insertar en la tabla persona: " . $stmt_persona->error);
    }

    $id_persona = $conn->insert_id;

    $sql_usuario = "INSERT INTO usuario (id_persona, username, password) VALUES (?, ?, ?)";
    $stmt_usuario = $conn->prepare($sql_usuario);
    if (!$stmt_usuario) {
        throw new Exception("Error en la preparación de la consulta usuario: " . $conn->error);
    }
    $stmt_usuario->bind_param("iss", $id_persona, $username, $hashed_password);
    if (!$stmt_usuario->execute()) {
        throw new Exception("Error al insertar en la tabla usuario: " . $stmt_usuario->error);
    }

    $id_usuario = $conn->insert_id;

    do {
        $numero_cuenta = str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT); 

        $sql_check_cuenta = "SELECT COUNT(*) as count FROM cuentas WHERE numero_cuenta = ?";
        $stmt_check_cuenta = $conn->prepare($sql_check_cuenta);
        $stmt_check_cuenta->bind_param("s", $numero_cuenta);
        $stmt_check_cuenta->execute();
        $result_check_cuenta = $stmt_check_cuenta->get_result();
        $row_check_cuenta = $result_check_cuenta->fetch_assoc();
    } while ($row_check_cuenta['count'] > 0); 
    $stmt_check_cuenta->close();

    $sql_cuenta = "INSERT INTO cuentas (id_usuario, numero_cuenta, saldo) VALUES (?, ?, 1000.00)";
    $stmt_cuenta = $conn->prepare($sql_cuenta);
    if (!$stmt_cuenta) {
        throw new Exception("Error en la preparación de la consulta cuenta: " . $conn->error);
    }
    $stmt_cuenta->bind_param("is", $id_usuario, $numero_cuenta);
    if (!$stmt_cuenta->execute()) {
        throw new Exception("Error al insertar en la tabla cuentas: " . $stmt_cuenta->error);
    }

    $conn->commit();

    header("Location: /integradora/signin_html.php?mensaje=" . urlencode("Registro exitoso. ¡Por favor, inicia sesión!"));
    exit();
} catch (Exception $e) {
    $conn->rollback();
    $mensaje="Error en el registro";
    header("Location: /integradora/register_html.php?mensaje=" . urlencode("Error en el registro: " . $e->getMessage()));
    exit();
} finally {
    if (isset($stmt_persona)) {
        $stmt_persona->close();
    }
    if (isset($stmt_usuario)) {
        $stmt_usuario->close();
    }
    if (isset($stmt_cuenta)) {
        $stmt_cuenta->close();
    }
}


$conn->close();
?>
