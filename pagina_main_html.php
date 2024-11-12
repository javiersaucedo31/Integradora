<?php
// Habilitar la visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar la sesión
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    header("Location: /integradora/signin.html?mensaje=" . urlencode("Por favor, inicia sesión primero."));
    exit();
}

// Incluir el archivo de conexión
include 'php/conextion.php';

// Recoger datos de la sesión
$username = $_SESSION['username'];

// Obtener la información del usuario y de la cuenta
$sql = "SELECT p.nombre, p.apellido, p.email, p.telefono, p.fecha_nac, c.numero_cuenta, c.saldo 
        FROM usuario u
        JOIN persona p ON u.id_persona = p.id_persona
        JOIN cuentas c ON u.id_usuario = c.id_usuario
        WHERE u.username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Verificar si se encontró el usuario
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nombre = $row['nombre'];
    $apellido = $row['apellido'];
    $email = $row['email'];
    $telefono = $row['telefono'];
    $fecha_nac = $row['fecha_nac'];
    $numero_cuenta = $row['numero_cuenta'];
    $saldo = $row['saldo'];
} else {
    // Si no se encontró al usuario, redirigir al inicio de sesión
    header("Location: /integradora/signin.html?mensaje=" . urlencode("Usuario no encontrado."));
    exit();
}

// Cerrar la conexión
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido-Appium</title>
    <link rel="stylesheet" href="css/estyle_pagmain.css?v=1.2">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <script src="js/main.js"></script>
   
   
</head>
<body>

<div class="navbar">
        <a href="#" class="logo">AppiumBanK</a>
        <a href="#" class="perf" onclick="openModal()"><i class="bi bi-person"></i>&nbsp;&nbsp;Mi perfil</a>
        <a href="#" class="historial-movimientos" onclick="openModal2()"><i class="bi bi-clock-history"></i>&nbsp;&nbsp;Historial Movimientos</a>
    </div>

    <div id="perfil" class="modal">
        <div class="modal-contenido">
            <span onclick="closeModal()" class="close">&times;</span>
            <section class="imagen">
                <img src="imagen/user.jpg" alt="user">
            </section>
            <section class="info" >
                <article class="user-perfil">Usuario: <?php echo htmlspecialchars($username); ?></article>
                <article class="name-perfil">Nombre: <?php echo htmlspecialchars($nombre) . " " . htmlspecialchars($apellido); ?></article>
                <article class="correo-perfil">Correo: <?php echo htmlspecialchars($email); ?></article>
                <article class="telefono-perfil">Teléfono: <?php echo htmlspecialchars($telefono); ?></article>
                <article class="fecha_nac-perfil">Fecha de nacimiento: <?php echo htmlspecialchars($fecha_nac); ?></article>
                <form class="form-cierre" action="php/cerrar_sesion.php" method="POST" style="display:block; margin-top: 20px;">
                     <button type="submit" class="boton-cerrarsesion">
                           Cerrar Sesión
                     </button>
                </form>
            </section>
        </div>
    </div>

    <div id="movements" class="modal">
       <div class="modal-contenido">
        <span onclick="closeModal2()" class="close">&times;</span>
        No hay movimientos recientes.
       </div>
    </div>

    <div class="informacion-Cuenta">
        <div class="credit-card">
            <section class="logo">AppiumBank</section>
            <section class="number"><?php echo htmlspecialchars($numero_cuenta); ?></section>
            <section class="details">
                <article class="holder"><?php echo htmlspecialchars($nombre) . " " . htmlspecialchars($apellido); ?></article>
                <article class="expiry">
                    <div>Válido hasta</div>
                    <div>12/25</div> <!-- Puedes actualizar dinámicamente esta información si tienes los datos de vencimiento -->
                </article>
            </section>
            <div class="network-logo"></div>
        </div>

        <div class="informacion-adicional">
            <section class="numero-tarjeta"><i class="bi bi-credit-card"></i>&nbsp; Número Tarjeta: <?php echo htmlspecialchars($numero_cuenta); ?></section>
            <section class="vencimiento"><i class="bi bi-calendar-event"></i>&nbsp; Vencimiento: 12/25</section> <!-- Vencimiento está hardcodeado, puedes ajustarlo si lo necesitas -->
            <section class="cve"><i class="bi bi-123"></i>&nbsp; CVE: 123</section> <!-- Puedes generar el CVE dinámicamente si lo tienes en la base de datos -->
            <section class="nombre-titular"><i class="bi bi-file-person"></i>&nbsp; Titular: <?php echo htmlspecialchars($nombre) . " " . htmlspecialchars($apellido); ?></section>
        </div>
    </div>

    <div class="saldo">
        <i class="bi bi-cash-coin"></i> SALDO: <?php echo number_format($saldo, 2); ?> MXN
    </div>

    <hr>

    <div class="operaciones">
        <h1>Servicios</h1>
        <article class="transferir" onclick="Modaloperacion()">Transferencia <br> <i class="bi bi-arrow-left-right"></i></article>
    </div>

    <div id="realizar-operacion" class="modal">
        <div class="modal-contenido">
         <span onclick="closeoperacion()" class="close">&times;</span>

         <h2>Realiza tu operación</h2>

         <form class="operation-form" action="php/transaccion.php" method="POST">
            <label for="cuenta-destino">Ingresa Destinatario:</label>
           <input type="text" name="cuenta-destino" id="cuenta-destino" required>

           <label for="monto">Ingresa el monto:</label>
           <input type="number" step="0.01" name="monto" id="monto" required>

           <button type="submit">Realizar Operación</button>
         </form>
        </div>
     </div>

    
</body>
</html>