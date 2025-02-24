<?php
require 'vendor/autoload.php';
require 'conexion.php';

use Mpdf\Mpdf;

if (!isset($_GET['dni'])) {
    die("❌ DNI no proporcionado.");
}

$dni = $_GET['dni'];
$stmt = $conn->prepare("SELECT nombre, dni, legajo, turno, carrera, qr FROM usuarios WHERE dni = ?");
$stmt->bind_param("s", $dni);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    die("❌ Usuario no encontrado.");
}

// Generar contenido del PDF
$html = "
    <style>
        .credencial {
            width: 85.6mm;
            height: 54mm;
            border-radius: 10px;
            background: #fff;
            text-align: left;
            font-family: Arial, sans-serif;
            border: 1px solid black;
            padding: 10px;
            position: relative;
        }
        .banda-negra {
            width: 100%;
            height: 10mm;
            background: black;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .datos {
            margin-top: 15px;
            font-size: 10pt;
        }
        .qr-container {
            position: absolute;
            bottom: 5mm;
            right: 5mm;
        }
    </style>
    <div class='credencial'>
        <div class='banda-negra'></div>
        <h4>{$usuario['nombre']}</h4>
        <p><b>Legajo:</b> {$usuario['legajo']}</p>
        <p><b>DNI:</b> {$usuario['dni']}</p>
        <p><b>Turno:</b> {$usuario['turno']}</p>
        <p><b>Carrera:</b> {$usuario['carrera']}</p>
        <div class='qr-container'><img src='{$usuario['qr']}' width='40mm'></div>
    </div>
";

$mpdf = new Mpdf(['format' => [85.6, 54]]);
$mpdf->WriteHTML($html);
$mpdf->Output("Credencial_{$usuario['dni']}.pdf", "D");
?>
