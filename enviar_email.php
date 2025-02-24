<?php
require 'vendor/autoload.php';
require 'conexion.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Mpdf\Mpdf;

if (!isset($_GET['dni'])) {
    die("❌ DNI no proporcionado.");
}

$dni = $_GET['dni'];
$stmt = $conn->prepare("SELECT nombre, email, legajo, turno, carrera FROM usuarios WHERE dni = ?");
$stmt->bind_param("s", $dni);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    die("❌ Usuario no encontrado.");
}

$mail = new PHPMailer(true);

try {
    // Configurar Gmail SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'juliancancelo@gmail.com'; // Tu Gmail
    $mail->Password = 'adqn juav lcky aljw'; // Contraseña de Aplicación de Gmail
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Configurar remitente y destinatario
    $mail->setFrom('juliancancelo@gmail.com', 'Asistencia QR');
    $mail->addAddress($usuario['email'], $usuario['nombre']);

    // Generar el código QR en el servidor
    $qrCode = new QrCode($dni);
    $writer = new PngWriter();
    $result = $writer->write($qrCode);

    // Guardar la imagen QR en el servidor
    $qr_image = "temp_qr_{$dni}.png";
    file_put_contents($qr_image, $result->getString());

    // Generar la credencial en PDF con mPDF con un diseño mejorado
    $mpdf = new Mpdf();
    $html = "
        <style>
            .credencial {
                width: 85.6mm;
                height: 54mm;
                border-radius: 10px;
                background: linear-gradient(135deg, #007bff, #0056b3);
                color: white;
                text-align: left;
                font-family: Arial, sans-serif;
                border: 2px solid black;
                padding: 10px;
                position: relative;
                box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.3);
            }
            .banda-negra {
                width: 100%;
                height: 12mm;
                background: black;
                border-top-left-radius: 10px;
                border-top-right-radius: 10px;
                position: absolute;
                top: 0;
                left: 0;
            }
            .logo {
                width: 50px;
                position: absolute;
                top: 15px;
                left: 15px;
            }
            .datos {
                margin-top: 25px;
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
            <img src='img/logo.png' class='logo'>
            <div class='datos'>
                <h4>{$usuario['nombre']}</h4>
                <p><b>Legajo:</b> {$usuario['legajo']}</p>
                <p><b>DNI:</b> {$dni}</p>
                <p><b>Turno:</b> {$usuario['turno']}</p>
                <p><b>Carrera:</b> {$usuario['carrera']}</p>
            </div>
            <div class='qr-container'>
                <p><b>Escanea este QR:</b></p>
                <img src='{$qr_image}'>
            </div>
        </div>
    ";
    $pdf_file = "credencial_{$dni}.pdf";
    $mpdf->WriteHTML($html);
    $mpdf->Output($pdf_file, \Mpdf\Output\Destination::FILE);

    // Asunto y cuerpo del correo con HTML mejorado
    $mail->Subject = '🎫 Tu Credencial de Asistencia QR';
    $mail->isHTML(true);
    $mail->Body = "
        <html>
        <head>
            <style>
                .email-container {
                    font-family: Arial, sans-serif;
                    color: #333;
                    padding: 20px;
                    text-align: center;
                    background-color: #f8f9fa;
                    border-radius: 10px;
                    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
                }
                .email-container h2 {
                    color: #007bff;
                }
                .email-container p {
                    font-size: 16px;
                }
                .qr-img {
                    margin-top: 10px;
                }
                .footer {
                    margin-top: 20px;
                    font-size: 12px;
                    color: #777;
                }
                .logo {
                    width: 100px;
                }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <img src='cid:logo' class='logo' alt='Logo'>
                <h2>¡Hola {$usuario['nombre']}! 🎉</h2>
                <p>Aquí tienes tu credencial de asistencia con tu código QR.</p>
                <p>📌 <strong>Legajo:</strong> {$usuario['legajo']}<br>
                📌 <strong>DNI:</strong> {$dni}<br>
                📌 <strong>Turno:</strong> {$usuario['turno']}<br>
                📌 <strong>Carrera:</strong> {$usuario['carrera']}</p>
                <div class='qr-img'>
                    <img src='cid:qr_code' width='150'>
                </div>
                <p><strong>Adjunto encontrarás tu credencial en PDF.</strong></p>
                <p class='footer'>Asistencia QR - Enviado automáticamente.</p>
            </div>
        </body>
        </html>
    ";

    // Adjuntar la credencial en PDF
    $mail->addAttachment($pdf_file, "Credencial_{$dni}.pdf");

    // Adjuntar la imagen QR y el logo en línea para mostrarlo en el correo
    $mail->addEmbeddedImage('img/logo.png', 'logo');
    $mail->addEmbeddedImage($qr_image, 'qr_code');

    // Enviar el correo
    $mail->send();
    echo "✅ Correo enviado correctamente.";

    // Eliminar los archivos temporales después de enviarlos
    unlink($qr_image);
    unlink($pdf_file);

} catch (Exception $e) {
    echo "❌ Error al enviar email: {$mail->ErrorInfo}";
}
?>
