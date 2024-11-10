<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SignUp</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/estilo_signup.css">
    
     <script src="script.js" defer></script>
</head>
<body>

        <div class="navbar">
            <a href="#" class="logo" onclick="loadView('pagina_inicio.html')">AppiumBanK</a>
            <button class="burger-menu" onclick="toggleMenu()">☰ menu</button>
            <ul class="nav-links" id="navLinks">
                <li><a href="#" class="inicio" onclick="loadView('pagina_inicio.html')"> Inicio</a></li>
                
                <li><a href="#" class="signin" onclick="loadView('signin.html')"> Iniciar sesión</a></li>
                <li><a href="#" class="signup" onclick="loadView('register_html.php')"> Registro</a></li>
            </ul>
        </div>


    
    <div id="contenido-registro">
        <div class="register-container">
            <img src="imagen/fondo.jpg" alt="signin">
            <div id="form-register" class="form-register">
                <h4>Regístrate</h4>
                <?php
                    if (isset($_GET['mensaje'])) {
                        $mensaje = htmlspecialchars($_GET['mensaje']);
                        echo "<div id='mensaje'>" . $mensaje . "</div>";
                    }
                ?>

                <form id="registro" action="php/register.php" method="POST">
                    <label for="nombre">Nombre(s):</label>
                    <input type="text" id="nombre" name="nombre" required>
    
                    <label for="apellido">Apellidos:</label>
                    <input type="text" id="apellido" name="apellido" required>
    
                    <label for="correo">Correo:</label>
                    <input type="email" id="correo" name="correo" required placeholder="mail@mail.com">
    
                    <label for="telefono">Teléfono:</label>
                    <input type="tel" id="telefono" name="telefono" required pattern="[0-9]{10}" placeholder="Ejemplo: 1234567890">
    
                    <label for="fecha_nac">Fecha de nacimiento:</label>
                    <input type="date" name="fecha_nac" id="fecha_nac" required>
    
                    <label for="register-username">Usuario:</label>
                    <input type="text" id="register-username" name="register-username" required>
    
                    <label for="password-register">Contraseña:</label>
                    <input type="password" id="password-register" name="password-register" required>
    
                    <hr>
                    <button type="submit">Registrar</button>
                </form>
                <!-- Contenedor para mensajes de error o éxito -->
                <div id="mensaje" style="color: red; margin-top: 10px;"></div>
            </div>
        </div>
    </div>
    
   
       





   <script src="js/register.js" ></script>
    
</body>
</html>