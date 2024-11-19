<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Verificar si el usuario está autenticado y tiene rol de administrador
if (!isset($_SESSION['username']) || $_SESSION['id_rol'] != 2) {
    $_SESSION['error'] = "Acceso solo para administradores";
    header("Location: /integradora/signin_html.php");
    exit();
}

include 'php/conextion.php';

$username = $_SESSION['username'];

// Obtener información del administrador (usuario actual)
$sql_admin = "SELECT p.nombre, p.apellido, p.email, p.telefono, p.fecha_nac
              FROM usuario u
              JOIN persona p ON u.id_persona = p.id_persona
              WHERE u.username = ?";
$stmt_admin = $conn->prepare($sql_admin);
$stmt_admin->bind_param("s", $username);
$stmt_admin->execute();
$result_admin = $stmt_admin->get_result();

if ($result_admin->num_rows > 0) {
    $admin_data = $result_admin->fetch_assoc();
} else {
    $_SESSION['error'] = "Error al cargar datos del admin";
    header("Location: /integradora/signin_html.php");
    exit();
}

// Obtener información de todos los usuarios
$sql_users = "SELECT u.id_usuario, u.username, p.nombre, p.apellido, p.email, p.telefono 
              FROM usuario u
              JOIN persona p ON u.id_persona = p.id_persona";
$result_users = $conn->query($sql_users);

$sql_transacciones = "SELECT t.id_transaccion, o.nombre, t.id_cuenta_origen, t.id_cuenta_destino, t.monto, t.fecha_transaccion, t.descripcion
              FROM transacciones t
              join operacion o on t.id_operacion=o.id_operacion";
$result_transacciones = $conn->query($sql_transacciones);

$sql_cuentas = "SELECT c.id_cuenta, c.id_usuario, c.numero_cuenta, c.saldo,c.fecha_apertura
              FROM cuentas c";
$result_cuentas = $conn->query($sql_cuentas);

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Appium</title>
    <link rel="stylesheet" href="css/estyle_pagmain.css?v=5.0">
    <link rel="stylesheet" href="css/admin_estilos.css?v=5.1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <script src="js/admin.js?v=1.0"></script>
</head>
<body>

    <div class="navbar">
        <a href="#" class="logo">AppiumBanK - Administrador</a>
        <a href="#" class="perf" onclick="openModal_admin()"><i class="bi bi-person"></i>&nbsp;&nbsp;Mi perfil</a>
    </div>

    <!-- Modal de perfil del administrador -->
    <div id="perfil-admin" class="modal">
        <div class="modal-contenido">
            <span onclick="closeModal_admin()" class="close">&times;</span>
            <section class="imagen">
                <img src="imagen/user.jpg" alt="admin">
            </section>
            <section class="info">
                <article class="user-perfil">Usuario: <?php echo htmlspecialchars($username); ?></article>
                <article class="name-perfil">Nombre: <?php echo htmlspecialchars($admin_data['nombre']) . " " . htmlspecialchars($admin_data['apellido']); ?></article>
                <article class="correo-perfil">Correo: <?php echo htmlspecialchars($admin_data['email']); ?></article>
                <article class="telefono-perfil">Teléfono: <?php echo htmlspecialchars($admin_data['telefono']); ?></article>
                <article class="fecha_nac-perfil">Fecha de nacimiento: <?php echo htmlspecialchars($admin_data['fecha_nac']); ?></article>
                <form class="form-cierre" action="php/cerrar_sesion.php" method="POST" style="display:block; margin-top: 20px;">
                    <button type="submit" class="boton-cerrarsesion">Cerrar Sesión</button>
                </form>
            </section>
        </div>
    </div>

    <!-- Tabla con todos los usuarios -->
    <div class="tabla-usuarios">
        <h2>Usuarios Registrados</h2>
        <table class="styled-table">
            <tr>
                <th>ID Usuario</th>
                <th>Username</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Acciones</th>
            </tr>
            <?php while ($usuario = $result_users->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $usuario['id_usuario']; ?></td>
                    <td><?php echo htmlspecialchars($usuario['username']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['apellido']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['telefono']); ?></td>
                    <td>
                    <a href="editar_usuario.php?id=<?php echo $usuario['id_usuario']; ?>" class="btn edit">Editar</a>
                    <a href="php/eliminar_usuario.php?id=<?php echo $usuario['id_usuario']; ?>" class="btn delete" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <div class="tabla-transacciones">
        <h3>Transacciones Realizadas </h3>
        <table class="styled-table">
            <tr>
                <th>ID Transaccion</th>
                <th>Operacion</th>
                <th>Cuenta Origen</th>
                <th>Cuenta Destino</th>
                <th>Monto</th>
                <th>Fecha transaccion</th>
                <th>Descripcion</th>
            </tr>
            <?php while ($transacciones = $result_transacciones->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $transacciones['id_transaccion']; ?></td>
                    <td><?php echo htmlspecialchars($transacciones['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($transacciones['id_cuenta_origen']); ?></td>
                    <td><?php echo htmlspecialchars($transacciones['id_cuenta_destino']); ?></td>
                    <td><?php echo htmlspecialchars($transacciones['monto']); ?></td>
                    <td><?php echo htmlspecialchars($transacciones['fecha_transaccion']); ?></td>
                    <td><?php echo htmlspecialchars($transacciones['descripcion']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <div class="tabla-cuentas">
    <h4>Cuentas</h4>
    <table class="styled-table">
        <tr>
            <th>ID Cuenta</th>
            <th>Id Usuario</th>
            <th>Numero Cuenta</th>
            <th>Saldo</th>
            <th>fecha apertura</th>
            
        </tr>
        <?php while ($cuentas = $result_cuentas->fetch_assoc()): ?>
            <tr>
                <td><?php echo $cuentas['id_cuenta']; ?></td>
                <td><?php echo htmlspecialchars($cuentas['id_usuario']); ?></td>
                <td><?php echo htmlspecialchars($cuentas['numero_cuenta']); ?></td>
                <td><?php echo htmlspecialchars($cuentas['saldo']); ?></td>
                <td><?php echo htmlspecialchars($cuentas['fecha_apertura']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

   
</body>
</html>
