<?php
require 'conexion.php';

$query = $conn->query("SELECT carrera, COUNT(*) as cantidad FROM asistencias GROUP BY carrera");
$carreras = [];
$cantidad = [];

while ($row = $query->fetch_assoc()) {
    $carreras[] = $row['carrera'];
    $cantidad[] = $row['cantidad'];
}

// Enviar JSON con los datos
echo json_encode(["carreras" => $carreras, "cantidad" => $cantidad]);
?>
