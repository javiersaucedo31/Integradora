<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Verificar si el usuario actual tiene rol de administrador
if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 2) {
    $_SESSION['error'] = "Acceso denegado";
    header("Location: /integradora/signin_html.php");
    exit();
}

include 'conextion.php';

if (isset($_GET['id'])) {
    $id_usuario = intval($_GET['id']);

    // Verificar que el usuario a eliminar no sea administrador
    $sql_check_role = "SELECT id_rol FROM usuario WHERE id_usuario = ?";
    $stmt_check_role = $conn->prepare($sql_check_role);
    $stmt_check_role->bind_param("i", $id_usuario);
    $stmt_check_role->execute();
    $result_check_role = $stmt_check_role->get_result();

    if ($result_check_role->num_rows > 0) {
        $row = $result_check_role->fetch_assoc();
        
        // Si el usuario es administrador, evitar su eliminación
        if ($row['id_rol'] == 2) {
            header("Location: /integradora/admin_dashboard.php?mensaje=" . urlencode("No se puede eliminar un administrador."));
            exit();
        }
    } else {
        header("Location: /integradora/admin_dashboard.php?mensaje=" . urlencode("Usuario no encontrado."));
        exit();
    }

    // Iniciar transacción para asegurarse de que ambas eliminaciones se hagan de manera atómica
    $conn->begin_transaction();

    try {
        // Obtener el id_persona asociado al id_usuario
        $sql_persona = "SELECT id_persona FROM usuario WHERE id_usuario = ?";
        $stmt_persona = $conn->prepare($sql_persona);
        $stmt_persona->bind_param("i", $id_usuario);
        $stmt_persona->execute();
        $result_persona = $stmt_persona->get_result();

        if ($result_persona->num_rows > 0) {
            $row = $result_persona->fetch_assoc();
            $id_persona = $row['id_persona'];

            // Eliminar el usuario de la tabla usuario
            $sql_usuario = "DELETE FROM usuario WHERE id_usuario = ?";
            $stmt_usuario = $conn->prepare($sql_usuario);
            $stmt_usuario->bind_param("i", $id_usuario);
            $stmt_usuario->execute();

            // Eliminar la persona de la tabla persona
            $sql_persona_delete = "DELETE FROM persona WHERE id_persona = ?";
            $stmt_persona_delete = $conn->prepare($sql_persona_delete);
            $stmt_persona_delete->bind_param("i", $id_persona);
            $stmt_persona_delete->execute();

            // Confirmar la transacción
            $conn->commit();
            header("Location: /integradora/admin_dashboard.php?mensaje=" . urlencode("Usuario y datos personales eliminados exitosamente."));
        } else {
            // Si no se encuentra el id_persona asociado, cancelar la transacción
            $conn->rollback();
            header("Location: /integradora/admin_dashboard.php?mensaje=" . urlencode("Error: Usuario no encontrado."));
        }
    } catch (Exception $e) {
        // En caso de error, deshacer la transacción
        $conn->rollback();
        header("Location: /integradora/admin_dashboard.php?mensaje=" . urlencode("Error al eliminar el usuario: " . $e->getMessage()));
    }

    $stmt_persona->close();
    $stmt_usuario->close();
    $stmt_persona_delete->close();
    $stmt_check_role->close();
} else {
    header("Location: /integradora/admin_dashboard.php?mensaje=" . urlencode("ID de usuario no especificado."));
}

$conn->close();
?>
