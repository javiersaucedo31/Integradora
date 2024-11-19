<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['username'])) {
    $_SESSION['error'] = "Inicia sesion primero, Por favor";
    header("Location: /integradora/signin_html.php");
    exit();
}


include 'php/conextion.php';


$username = $_SESSION['username'];

$sql = "SELECT p.nombre, p.apellido, p.email, p.telefono, p.fecha_nac, c.numero_cuenta, c.saldo,u.id_usuario,c.cve
        FROM usuario u
        JOIN persona p ON u.id_persona = p.id_persona
        JOIN cuentas c ON u.id_usuario = c.id_usuario
        WHERE u.username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();



if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nombre = $row['nombre'];
    $apellido = $row['apellido'];
    $email = $row['email'];
    $telefono = $row['telefono'];
    $fecha_nac = $row['fecha_nac'];
    $numero_cuenta = $row['numero_cuenta'];
    $saldo = $row['saldo'];
    $id_usuario = $row['id_usuario'];
    
    $cve = $row['cve'];
} else {
    $_SESSION['error'] = "Usuario no encontrado";
    header("Location: /integradora/signin_html.php");
    exit();
}

$sql_mistransacciones = "
   SELECT 
    o.nombre AS operacion, 
    c_destino.numero_cuenta AS destinatario, 
    t.monto, 
    t.fecha_transaccion, 
    t.descripcion 
FROM transacciones t
JOIN operacion o ON t.id_operacion = o.id_operacion
JOIN cuentas c_origen ON t.id_cuenta_origen = c_origen.id_cuenta
JOIN cuentas c_destino ON t.id_cuenta_destino = c_destino.id_cuenta
JOIN usuario u ON c_origen.id_usuario = u.id_usuario
WHERE u.id_usuario = ?
ORDER BY t.fecha_transaccion DESC;

";
$stmt_usertransacciones = $conn->prepare($sql_mistransacciones);

if (!$stmt_usertransacciones) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

// Vinculamos el número de cuenta
$stmt_usertransacciones->bind_param("s", $id_usuario);

// Ejecutamos la consulta
if (!$stmt_usertransacciones->execute()) {
    die("Error al ejecutar la consulta: " . $stmt_usertransacciones->error);
}

$result_usertransacciones = $stmt_usertransacciones->get_result();


$stmt_usertransacciones->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido-Appium</title>
    <link rel="stylesheet" href="css/estyle_pagmain.css?v=7.1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <script src="js/main.js?v=9.1"></script>
   
   
</head>
<body>

    <div class="navbar">
        <a href="#" class="logo">Appium<span class="bk">BanK</span></a>
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
                     <button type="submit" class="boton-cerrarsesion" onclick="return confirm('¿Estás seguro de que deseas salir de la sesion?');">
                           Cerrar Sesión
                     </button>
                </form>
            </section>
        </div>
    </div>

    <div id="movements" class="modal">
       <div class="modal-contenido">
        <span onclick="closeModal2()" class="close">&times;</span>
        <table class="tabla-movimientos">
            <tr>
                <th>Operacion</th>
                <th>Destinatario</th>
                <th>Monto</th>
                <th>Fecha transaccion</th>
                <th>Descripcion</th>
            </tr>
            <?php while ($movimientos = $result_usertransacciones->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars ($movimientos['operacion']); ?></td>
                    <td><?php echo htmlspecialchars($movimientos['destinatario']); ?></td>
                    <td><?php echo htmlspecialchars($movimientos['monto']); ?></td>
                    <td><?php echo htmlspecialchars($movimientos['fecha_transaccion']); ?></td>
                    <td><?php echo htmlspecialchars($movimientos['descripcion']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
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
                    <div>12/26</div> 
                </article>
            </section>
            <div class="network-logo"></div>
        </div>

        <div class="informacion-adicional">
            <section class="numero-tarjeta"><i class="bi bi-credit-card"></i>&nbsp; Número Tarjeta: <?php echo htmlspecialchars($numero_cuenta); ?></section>
            <section class="vencimiento"><i class="bi bi-calendar-event"></i>&nbsp; Vencimiento: 12/26</section> 
            <section class="cve"><i class="bi bi-123"></i>&nbsp; CVE: <?php echo htmlspecialchars($cve); ?> </section> 
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


    <?php
        if (isset($_SESSION['success'])) {
         echo "<div style='
            color: #fff;
            background-color: #3CB371;
            padding: 15px;
            border-radius: 5px;
            max-width: 400px;
            text-align: center;
            margin: 20px auto;
            font-weight: bold;'
          >" . $_SESSION['success'] . "</div>";
            unset($_SESSION['success']); // Eliminar el mensaje de éxito después de mostrarlo
        }
    ?>

   
    <div id="realizar-operacion" class="modal">
        <div class="modal-contenido">
         <span onclick="closeoperacion()" class="close">&times;</span>

         <button class="boton-alternar" onclick="mostrarFormulario('guardar_contacto')" type="button">Guardar Contacto</button>
        <button class="boton-alternar" onclick="mostrarFormulario('operation_form')" type="button">Realizar Operación</button>
        
        <!-- Formulario para guardar contacto -->
        <form id="guardar_contacto" class="guardar-contacto" action="php/guardar_contacto.php" method="POST" style="display: none;">
            
            <?php
                    if (isset($_SESSION['errore'])) {
                        echo "<p style='color: red;'>" . $_SESSION['errore'] . "</p>";
                        unset($_SESSION['errore']); // Eliminar el mensaje de error después de mostrarlo
                    }
                ?>


            <label for="nombre-contacto">Nombre del Contacto:</label>
            <input type="text" id="nombre-contacto" name="nombre-contacto" required>
        
            <label for="cuenta-destino-guardar">Número de Cuenta:</label>
            <input type="text" id="cuenta-destino-guardar" name="cuenta-destino-guardar" required>
        
            <button type="submit">Guardar Contacto</button>
        </form>

        <!-- Formulario para realizar operación -->
        <form id="operation_form" class="operation-form" action="php/transaccion.php" method="POST" style="display: none;">
                <?php
                    if (isset($_SESSION['error'])) {
                        echo "<p style='color: red;'>" . $_SESSION['error'] . "</p>";
                        unset($_SESSION['error']); // Eliminar el mensaje de error después de mostrarlo
                    }
                ?>

                <select id="contacto-destino" onchange="llenarCuentaDestino(this)">
                    <option value="">Selecciona un contacto</option>
                         <?php
                         include 'php/conextion.php';
                        $stmt = $conn->prepare("SELECT nombre_contacto, numero_cuenta FROM contactos WHERE id_usuario = ?");
                        $stmt->bind_param("i", $id_usuario);
                        $stmt->execute();
                        $result = $stmt->get_result();
               
                    if ($result->num_rows > 0) {
                        while ($contacto = $result->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($contacto['numero_cuenta']) . "'>" . htmlspecialchars($contacto['nombre_contacto']) . "</option>";
                    }
                        } else {
                    echo "<option value=''>No hay contactos disponibles</option>";
                }

                $stmt->close();
                ?>
            </select>



            <label for="cuenta-destino">Ingresa Destinatario:</label>
            <input type="text" name="cuenta-destino" id="cuenta-destino">

            <label for="monto">Ingresa el monto:</label>
            <input type="number" name="monto" id="monto">
            
            <br>

            <button type="submit">Realizar Operación</button>
        </form>
        </div>
     </div>


     <script src="js/main.js?v=8.1"></script>
    
</body>
</html>