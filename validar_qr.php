<?php
require 'conexion.php';

if (!isset($_GET['dni'])) {
    echo json_encode(["status" => false, "message" => "❌ Código QR no válido."]);
    exit;
}

$dni = $_GET['dni'];
$stmt = $conn->prepare("SELECT nombre, legajo, turno, carrera FROM usuarios WHERE dni = ?");
$stmt->bind_param("s", $dni);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    echo json_encode(["status" => false, "message" => "❌ Acceso denegado. Usuario no encontrado."]);
    exit;
}

// Determinar si es entrada o salida
$ultimo_registro = $conn->prepare("SELECT tipo_acceso FROM asistencias WHERE dni = ? ORDER BY fecha_hora DESC LIMIT 1");
$ultimo_registro->bind_param("s", $dni);
$ultimo_registro->execute();
$ultimo_resultado = $ultimo_registro->get_result()->fetch_assoc();
$tipo_acceso = ($ultimo_resultado && $ultimo_resultado['tipo_acceso'] == 'Entrada') ? 'Salida' : 'Entrada';

// Registrar asistencia con los datos completos
$stmt = $conn->prepare("INSERT INTO asistencias (dni, nombre, legajo, turno, carrera, tipo_acceso) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $dni, $usuario['nombre'], $usuario['legajo'], $usuario['turno'], $usuario['carrera'], $tipo_acceso);
$stmt->execute();

echo json_encode(["status" => true, "message" => "✅ {$tipo_acceso} registrada para {$usuario['nombre']}"]);
?>
