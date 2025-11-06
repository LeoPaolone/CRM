<?php
// Iniciar la sesión si aún no se ha iniciado
session_start();

// Destruir todas las variables de sesión
$_SESSION = array();

// Destruir la sesión
session_destroy();

// Redirigir al usuario a la página de inicio de sesión o a la página principal
header("Location: ../auth/login.php"); // Asume que tienes una página de login.php
exit;
?>