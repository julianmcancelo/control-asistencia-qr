<?php require 'conexion.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escaneo de QR en Tiempo Real</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="assets/js/jsQR.js"></script> 
    <style>
        body {
            background-color: #f5f5f5;
            color: #333;
            font-family: 'Arial', sans-serif;
        }
        .scanner-container {
            width: 100%;
            max-width: 500px;
            margin: auto;
            padding: 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
        }
        h2 {
            color: #007bff;
            font-size: 22px;
        }
        .video-container {
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            border: 4px solid #007bff;
            box-shadow: 0px 0px 20px rgba(0, 123, 255, 0.3);
        }
        video {
            width: 100%;
            border-radius: 10px;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 5px;
            text-align: center;
        }
        .btn-back {
            margin-top: 20px;
            background: #007bff;
            color: white;
            padding: 12px;
            border-radius: 10px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .btn-back:hover {
            background: #0056b3;
        }
        .manual-input {
            margin-top: 20px;
            padding: 10px;
            background: #eef;
            border-radius: 10px;
        }
        .manual-input input {
            width: 80%;
            padding: 10px;
            border-radius: 5px;
            border: none;
            text-align: center;
        }
        .manual-input button {
            margin-top: 10px;
            background: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }
        .manual-input button:hover {
            background: #218838;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="scanner-container">
        <h2>📷 Escanear Código QR</h2>
        <div class="video-container">
            <video id="video" autoplay playsinline></video>
        </div>
        <canvas id="canvas" style="display: none;"></canvas>
        <div id="result" class="result alert alert-info">Escanea un código QR...</div>
        
        <div class="manual-input">
            <h4>🔑 Ingreso Manual</h4>
            <input type="text" id="dniInput" placeholder="Ingrese su DNI">
            <button onclick="manualAccess()">Ingresar</button>
        </div>
        
        <a href="index.php" class="btn-back">⬅ Volver al Inicio</a>
    </div>
</div>

<script>
    let video = document.getElementById("video");
    let canvasElement = document.getElementById("canvas");
    let canvas = canvasElement.getContext("2d");
    let resultElement = document.getElementById("result");
    let scanning = true;

    async function startCamera() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } });
            video.srcObject = stream;
            requestAnimationFrame(scanQR);
        } catch (error) {
            console.error("Error al acceder a la cámara: ", error);
            resultElement.innerHTML = "❌ Error al acceder a la cámara. Asegúrese de permitir el acceso.";
            resultElement.classList.add("alert-danger");
        }
    }

    function scanQR() {
        if (!scanning) return;
        
        if (video.readyState === video.HAVE_ENOUGH_DATA) {
            canvasElement.width = video.videoWidth;
            canvasElement.height = video.videoHeight;
            canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
            const imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
            const qrCode = jsQR(imageData.data, imageData.width, imageData.height);

            if (qrCode) {
                scanning = false;
                video.pause();
                sendToServer(qrCode.data);
                setTimeout(() => {
                    scanning = true;
                    video.play();
                    requestAnimationFrame(scanQR);
                }, 3000);
            } else {
                requestAnimationFrame(scanQR);
            }
        } else {
            requestAnimationFrame(scanQR);
        }
    }

    function sendToServer(qrData) {
        fetch("validar_qr.php?dni=" + qrData)
            .then(response => response.json())
            .then(data => {
                resultElement.innerHTML = data.message;
                resultElement.classList.remove("alert-info", "alert-danger", "alert-success");
                resultElement.classList.add(data.status ? "alert-success" : "alert-danger");
            })
            .catch(error => {
                resultElement.innerHTML = "❌ Error al procesar el QR.";
                resultElement.classList.add("alert-danger");
            });
    }

    function manualAccess() {
        const dni = document.getElementById("dniInput").value;
        if (dni) {
            sendToServer(dni);
        }
    }

    startCamera();
</script>

</body>
</html>
