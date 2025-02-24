<?php
require 'vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

if (isset($_GET['dni'])) {
    $dni = $_GET['dni'];

    // Crear el código QR
    $qrCode = new QrCode($dni);
    $writer = new PngWriter();
    $result = $writer->write($qrCode);

    // Devolver la imagen QR al navegador
    header('Content-Type: ' . $result->getMimeType());
    echo $result->getString();
} else {
    echo "❌ No se proporcionó un DNI válido.";
}
?>
