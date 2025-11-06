<?php
// Iniciar la sesión al principio de cada script que incluya config.php
session_start();

$servername = "localhost";
$username = "root";     // Tu usuario de MySQL
$password = ""; // Tu contraseña de MySQL
$dbname = "crm_db";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
