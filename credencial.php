<?php
require 'conexion.php';

if (!isset($_GET['dni'])) {
    die("❌ DNI no proporcionado.");
}

$dni = $_GET['dni'];
$stmt = $conn->prepare("SELECT nombre, dni, legajo, turno, carrera, email, qr FROM usuarios WHERE dni = ?");
$stmt->bind_param("s", $dni);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    die("❌ Usuario no encontrado.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credencial de Alumno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            text-align: center;
            background-color: #f4f4f4;
        }

        /* Contenedor de la credencial */
        .credencial {
            width: 540px;
            height: 320px;
            border-radius: 15px;
            background: #ffffff;
            position: relative;
            padding: 20px;
            text-align: left;
            font-family: Arial, sans-serif;
            box-shadow: 5px 5px 20px rgba(0, 0, 0, 0.3);
            display: inline-block;
            margin-top: 20px;
            border: 2px solid #007bff;
        }

        /* Banda magnética */
        .banda-negra {
            position: absolute;
            top: 20px;
            left: 0;
            width: 100%;
            height: 50px;
            background: black;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        /* Logo */
        .logo {
            position: absolute;
            top: 80px;
            left: 20px;
            width: 80px;
        }

        /* Datos del alumno */
        .datos {
            position: absolute;
            top: 80px;
            left: 120px;
            width: 380px;
        }

        .datos h4 {
            margin: 0;
            font-size: 22px;
            font-weight: bold;
            color: #333;
        }

        .datos p {
            margin: 5px 0;
            font-size: 18px;
            color: #555;
        }

        /* Código QR */
        .qr-container {
            position: absolute;
            bottom: 20px;
            right: 20px;
        }

        .qr-container img {
            width: 100px;
        }

        /* Ajustes para impresión */
        @media print {
            body {
                background: white;
            }
            .credencial {
                width: 85.6mm;
                height: 54mm;
                box-shadow: none;
                border: 1px solid black;
                display: block;
                margin: auto;
                page-break-before: always;
            }
            .banda-negra {
                height: 10mm;
            }
            .logo {
                width: 20mm;
            }
            .datos h4 {
                font-size: 12pt;
            }
            .datos p {
                font-size: 10pt;
            }
            .qr-container img {
                width: 30mm;
            }
            button, .btn {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <div class="credencial">
        <div class="banda-negra"></div>
        <img src="img/logo.png" class="logo" alt="Logo">
        <div class="datos">
            <h4><?= htmlspecialchars($usuario['nombre']) ?></h4>
            <p><b>Legajo:</b> <?= htmlspecialchars($usuario['legajo']) ?></p>
            <p><b>DNI:</b> <?= htmlspecialchars($usuario['dni']) ?></p>
            <p><b>Turno:</b> <?= htmlspecialchars($usuario['turno']) ?></p>
            <p><b>Carrera:</b> <?= htmlspecialchars($usuario['carrera']) ?></p>
        </div>
        <div class="qr-container">
            <img src="<?= htmlspecialchars($usuario['qr']) ?>" alt="QR">
        </div>
    </div>

    <!-- Botones de acciones -->
    <div class="mt-3">
        <button onclick="window.print()" class="btn btn-primary">🖨️ Imprimir Credencial</button>
        <a href="descargar_credencial.php?dni=<?= $usuario['dni'] ?>" class="btn btn-danger">📥 Descargar PDF</a>
        <a href="enviar_email.php?dni=<?= urlencode($usuario['dni']) ?>" class="btn btn-info">📧 Enviar por Correo</a>
        <a href="index.php" class="btn btn-secondary">Volver</a>
    </div>
</div>

</body>
</html>
