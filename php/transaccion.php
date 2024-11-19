<?php
// Habilitar la visualización de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    $_SESSION['error'] = "Inicia sesion primero";
    header("Location: /integradora/signin_html.php");
    exit();
}

// Incluir el archivo de conexión
include 'conextion.php';

// Verificar que la conexión se estableció correctamente
try {
    if (!$conn) {
        throw new Exception("Error al conectar con la base de datos.");
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

$username = $_SESSION['username'];

// Recoger y validar datos del formulario
$cuenta_destino = trim($_POST['cuenta-destino']);
$monto = floatval($_POST['monto']);
$id_operacion = 1;

if ($monto <= 0) {
    $_SESSION['error'] = "El monto debe ser mayor a 0";
    header("Location: /integradora/pagina_main_html.php");
    exit();
}

// Obtener el número de cuenta de origen y el ID de la cuenta del usuario
$sql_origen = "SELECT c.id_cuenta, c.numero_cuenta, c.saldo 
               FROM usuario u
               JOIN cuentas c ON u.id_usuario = c.id_usuario
               WHERE u.username = ?";
$stmt = $conn->prepare($sql_origen);
$stmt->bind_param("s", $username);

if (!$stmt->execute()) {
    echo "Error en la consulta de cuenta origen: " . $stmt->error;
    exit();
}

$result_origen = $stmt->get_result();

if ($result_origen->num_rows > 0) {
    $origen = $result_origen->fetch_assoc();
    $id_cuenta_origen = $origen['id_cuenta'];
    $numero_cuenta_origen = $origen['numero_cuenta'];
    $saldo_origen = $origen['saldo'];

    if ($cuenta_destino == $numero_cuenta_origen) {
        $_SESSION['error'] = "No puedes transferir a tu misma cuenta";
        header("Location: /integradora/pagina_main_html.php");
        exit();
    }

    if ($saldo_origen < $monto) {
        $_SESSION['error'] = "Saldo insuficiente";
        header("Location: /integradora/pagina_main_html.php");
        exit();
    }

    // Verificar que la cuenta destino exista y obtener su id
    $sql_destino = "SELECT id_cuenta FROM cuentas WHERE numero_cuenta = ?";
    $stmt_destino = $conn->prepare($sql_destino);
    $stmt_destino->bind_param("s", $cuenta_destino);

    if (!$stmt_destino->execute()) {
        echo "Error en la consulta de cuenta destino: " . $stmt_destino->error;
        exit();
    }

    $result_destino = $stmt_destino->get_result();

    if ($result_destino->num_rows > 0) {
        $destino = $result_destino->fetch_assoc();
        $id_cuenta_destino = $destino['id_cuenta'];

        // La cuenta destino existe, proceder con la transferencia
        $conn->begin_transaction();
        try {
            // Restar saldo de la cuenta origen
            $sql_restar = "UPDATE cuentas SET saldo = saldo - ? WHERE id_cuenta = ?";
            $stmt_restar = $conn->prepare($sql_restar);
            $stmt_restar->bind_param("di", $monto, $id_cuenta_origen);
            if (!$stmt_restar->execute()) {
                throw new Exception("Error al restar saldo: " . $stmt_restar->error);
            }

            // Sumar saldo a la cuenta destino
            $sql_sumar = "UPDATE cuentas SET saldo = saldo + ? WHERE id_cuenta = ?";
            $stmt_sumar = $conn->prepare($sql_sumar);
            $stmt_sumar->bind_param("di", $monto, $id_cuenta_destino);
            if (!$stmt_sumar->execute()) {
                throw new Exception("Error al sumar saldo: " . $stmt_sumar->error);
            }

            // Registrar la transacción usando los ids de las cuentas
            $descripcion = "Transferencia de $numero_cuenta_origen a $cuenta_destino";
            $sql_transaccion = "INSERT INTO transacciones (id_operacion, id_cuenta_origen, id_cuenta_destino, monto, descripcion) 
                                VALUES (?, ?, ?, ?, ?)";
            $stmt_transaccion = $conn->prepare($sql_transaccion);
            $stmt_transaccion->bind_param("iiids", $id_operacion, $id_cuenta_origen, $id_cuenta_destino, $monto, $descripcion);
            if (!$stmt_transaccion->execute()) {
                throw new Exception("Error al registrar la transacción: " . $stmt_transaccion->error);
            }

            // Confirmar la transacción
            $conn->commit();
            $_SESSION['success'] = "Transferencia realizada con exito";
            header("Location: /integradora/pagina_main_html.php?mensaje=" . urlencode("Transferencia realizada con éxito."));
        } catch (Exception $e) {
            // Revertir cambios si hubo un error
            $conn->rollback();
            header("Location: /integradora/pagina_main_html.php?mensaje=" . urlencode("Error al realizar la transferencia: " . $e->getMessage()));
        }
    } else {
        $_SESSION['error'] = "La cuenta a la que quieres transferir no existe";
        header("Location: /integradora/pagina_main_html.php");
    }
} else {
    header("Location: /integradora/pagina_main_html.php?mensaje=" . urlencode("No se pudo obtener los datos de la cuenta de origen."));
}

$conn->close();
?>
