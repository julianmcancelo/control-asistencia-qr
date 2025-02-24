<?php
require 'vendor/autoload.php';
require 'conexion.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Mpdf\Mpdf;

// Si no se ha enviado un DNI o Legajo, mostrar el formulario de entrada
if (!isset($_GET['dni']) && !isset($_GET['legajo'])) {
    echo "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Generar Credencial</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
            <style>
                body {
                                background: linear-gradient(to right, #004aad, #002f6c);
            color: white;
            font-family: 'Arial', sans-serif;
                }
                .card {
                    background: white;
                    color: black;
                    border-radius: 10px;
                    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
                    padding: 20px;
                    text-align: center;
                }
                .btn-primary {
                    width: 100%;
                    font-size: 1.2rem;
                    padding: 10px;
                }
            </style>
        </head>
        <body>
            <div class='container d-flex justify-content-center align-items-center' style='height: 100vh;'>
                <div class='card p-4' style='width: 400px;'>
                    <h3 class='mb-3'>Generar Credencial</h3>
                    <form method='GET'>
                        <div class='mb-3'>
                            <label for='dni' class='form-label'>Ingrese DNI o Legajo</label>
                            <input type='text' class='form-control' name='dni' id='dni' required placeholder='Ejemplo: 12345678'>
                        </div>
                        <button type='submit' class='btn btn-primary'>🔍 Generar Credencial</button>
                    </form>
                </div>
            </div>
        </body>
        </html>
    ";
    exit;
}

// Obtener el dato ingresado (DNI o Legajo)
$dni = $_GET['dni'] ?? null;
$legajo = $_GET['legajo'] ?? null;

// Consultar usuario en la base de datos
if ($dni) {
    $stmt = $conn->prepare("SELECT nombre, dni, legajo, turno, carrera FROM usuarios WHERE dni = ?");
    $stmt->bind_param("s", $dni);
} elseif ($legajo) {
    $stmt = $conn->prepare("SELECT nombre, dni, legajo, turno, carrera FROM usuarios WHERE legajo = ?");
    $stmt->bind_param("s", $legajo);
}

$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    die("
        <div class='container text-center mt-5'>
            <div class='alert alert-danger' role='alert'>
                ❌ Usuario no encontrado. <br> <a href='generar_credencial.php' class='btn btn-outline-danger mt-2'>Volver</a>
            </div>
        </div>
    ");
}

// Generar el código QR con el DNI o Legajo
    $qrCode = new QrCode($usuario['dni']);
    $writer = new PngWriter();
    $result = $writer->write($qrCode);
    $qrPath = "temp_qr_{$usuario['dni']}.png";
    file_put_contents($qrPath, $result->getString());

    // Generar la credencial en PDF con "Instituto Beltran"
    $mpdf = new Mpdf();
    $html = "
        <style>
            .credencial {
                width: 85.6mm;
                height: 54mm;
                border-radius: 10px;
                background: linear-gradient(135deg, #007bff, #0056b3);
                color: white;
                font-family: Arial, sans-serif;
                border: 2px solid black;
                padding: 10px;
                position: relative;
                box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.3);
            }
            .banda-negra {
                width: 100%;
                height: 10mm;
                background: black;
                border-top-left-radius: 10px;
                border-top-right-radius: 10px;
            }
            .titulo {
                text-align: center;
                font-size: 14pt;
                font-weight: bold;
                margin-top: 5px;
            }
            .logo {
                width: 50px;
                position: absolute;
                top: 15px;
                left: 15px;
            }
            .datos {
                margin-top: 20px;
                font-size: 12pt;
                position: absolute;
                left: 15mm;
                top: 18mm;
            }
            .datos h4 {
                font-size: 14pt;
                margin: 0;
            }
            .datos p {
                margin: 3px 0;
                font-size: 11pt;
            }
            .qr-container {
                position: absolute;
                bottom: 8mm;
                right: 10mm;
                text-align: center;
                background: white;
                padding: 5px;
                border-radius: 5px;
            }
            .qr-container img {
                width: 40mm;
            }
        </style>
        <div class='credencial'>
            <div class='banda-negra'></div>
            <div class='titulo'>Instituto Beltran</div>
            <img src='img/logo.png' class='logo'>
            <div class='datos'>
                <h4>{$usuario['nombre']}</h4>
                <p><b>Legajo:</b> {$usuario['legajo']}</p>
                <p><b>DNI:</b> {$usuario['dni']}</p>
                <p><b>Turno:</b> {$usuario['turno']}</p>
                <p><b>Carrera:</b> {$usuario['carrera']}</p>
            </div>
            <div class='qr-container'>
                <p><b>Escanea este QR:</b></p>
                <img src='{$qrPath}'>
            </div>
        </div>
    ";
    $pdfFile = "credencial_{$usuario['dni']}.pdf";
    $mpdf->WriteHTML($html);
    $mpdf->Output($pdfFile, \Mpdf\Output\Destination::FILE);

// Mostrar vista previa mejorada
echo "
<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Credencial Generada</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
 body {
            background: linear-gradient(to right, #004aad, #002f6c);
            color: white;
            font-family: 'Arial', sans-serif;
        }
        .card {
            background: white;
            color: black;
            border-radius: 12px;
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.3);
            padding: 25px;
            text-align: center;
            animation: fadeIn 0.8s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .qr-preview {
            width: 250px;
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            border-radius: 10px;
        }
        .qr-preview:hover {
            transform: scale(1.1);
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.4);
        }
        .btn-custom {
            font-size: 1.2rem;
            padding: 12px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }
        .btn-custom i {
            margin-right: 8px;
        }
        .btn-custom:hover {
            transform: scale(1.05);
        }
        .confirmation-icon {
            font-size: 3rem;
            color: #28a745;
            animation: bounce 1s infinite alternate;
        }
        @keyframes bounce {
            from { transform: translateY(0); }
            to { transform: translateY(-5px); }
        }
    </style>
</head>
<body>

<div class='container d-flex justify-content-center align-items-center' style='height: 100vh;'>
    <div class='card p-4' style='width: 400px;'>
        <h2 class='mb-3'>Credencial Generada</h2>
        <p>Vista previa del QR:</p>
        <img src='{$qrPath}' class='img-fluid' style='width: 250px;'>
        <div class='mt-4'>
            <a href='{$pdfFile}' class='btn btn-success' download>📥 Descargar Credencial</a>
            <a href='generar_credencial.php' class='btn btn-outline-secondary mt-2'>Volver</a>
        </div>
    </div>
</div>

</body>
</html>
";

// Limpiar archivos temporales después de 1 minuto
register_shutdown_function(function () use ($qrPath, $pdfFile) {
    sleep(60);
    if (file_exists($qrPath)) unlink($qrPath);
    if (file_exists($pdfFile)) unlink($pdfFile);
});
?>
