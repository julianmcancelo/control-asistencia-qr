<?php
$host = "localhost";
$user = "root"; // Cambiar si es necesario
$pass = ""; // Cambiar si hay contraseña
$dbname = "control_asistencia";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
