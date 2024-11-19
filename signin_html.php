<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APPIUMBANK - Signin</title>
    <link rel="stylesheet" href="css/styles.css?v=2.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/estilo_signin.css?v=3.3">
    <script src="script.js?v=8.0" defer></script>
    
</head>
<body>
    
    <div id="main-content">
        <div class="navbar">
            <a href="#" class="logo" onclick="loadView('pagina_inicio.html')">Appium<span class="bk">BanK</span></a>
             <button class="burger-menu" onclick="toggleMenu()">☰</button>
                <ul class="nav-links" id="navLinks">
                     <li><a href="#" class="inicio" onclick="loadView('pagina_inicio.html')">Inicio</a></li>
                    <li><a href="#" class="signin" onclick="loadView('signin_html.php')"> Iniciar sesión</a></li>
                     <li><a href="#" class="signup" onclick="loadView('register_html.php')"> Registro</a></li>
                </ul>
        </div>
    
        <div class="login-container">
                <video autoplay loop muted playsinline class="mi_video" >
                  <source src="imagen/3129957-uhd_3840_2160_25fps.mp4" type="video/mp4">
                </video>
    
             <div id="form-login" class="form-login">
                <h4>Iniciar Sesion</h4>
                    <form action="php/signin.php" method="POST"> 

                        <?php
                     if (isset($_SESSION['error'])) {
                      echo "<p style='color: red;'>" . $_SESSION['error'] . "</p>";
                       unset($_SESSION['error']); // Eliminar el mensaje de error después de mostrarlo
                      }
                     ?>

                     <label for="username">Usuario:</label>
                     <input type="text" id="username" name="username" required >
                      
                     <label for="password">Contraseña:</label>
                     <input type="password" id="password" name="password" required>
            
                
                        <hr>
                    
                        <button type="submit">Ingresar</button>
                    </form>
            </div>
        </div>
    </div>
       
</body>
</html>