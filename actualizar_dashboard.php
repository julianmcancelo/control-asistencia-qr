<?php
require 'conexion.php';

// Contar total de usuarios y accesos
$total_usuarios = $conn->query("SELECT COUNT(*) as total FROM usuarios")->fetch_assoc()['total'];
$total_asistencias = $conn->query("SELECT COUNT(*) as total FROM asistencias")->fetch_assoc()['total'];

// Contar accesos de hoy
$accesos_hoy = $conn->query("SELECT COUNT(*) as total FROM asistencias WHERE DATE(fecha_hora) = CURDATE()")->fetch_assoc()['total'];

// Obtener últimos accesos
$ultimos_accesos_query = $conn->query("SELECT * FROM asistencias ORDER BY fecha_hora DESC LIMIT 5");
$ultimos_accesos_html = "";
while ($row = $ultimos_accesos_query->fetch_assoc()) {
    $ultimos_accesos_html .= "<tr>
        <td>{$row['nombre']}</td>
        <td>{$row['legajo']}</td>
        <td>{$row['carrera']}</td>
        <td class='".($row['tipo_acceso'] == 'Entrada' ? 'text-success' : 'text-danger')."'>{$row['tipo_acceso']}</td>
        <td>{$row['fecha_hora']}</td>
    </tr>";
}

// Enviar datos en JSON
echo json_encode([
    "total_usuarios" => $total_usuarios,
    "total_asistencias" => $total_asistencias,
    "accesos_hoy" => $accesos_hoy,
    "ultimos_accesos" => $ultimos_accesos_html,
]);
?>
