<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escanear Código QR</title>
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5 text-center">
    <div class="card shadow p-4">
        <h2>Escanea tu código QR</h2>
        <p>Selecciona una cámara y escanea el QR.</p>

        <!-- Selector de Cámara -->
        <select id="cameras" class="form-select mb-3"></select>

        <!-- Vista previa del escaneo -->
        <video id="preview" class="border rounded w-100"></video>
    </div>
</div>

<script>
    let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });

    // Función para cambiar de cámara
    function changeCamera(deviceId) {
        scanner.stop();
        scanner.start(new Instascan.Camera(deviceId));
    }

    // Obtener cámaras disponibles
    Instascan.Camera.getCameras().then(function(cameras) {
        if (cameras.length > 0) {
            let cameraSelect = document.getElementById('cameras');
            
            // Agregar cámaras a la lista desplegable
            cameras.forEach((camera, index) => {
                let option = document.createElement('option');
                option.value = camera.id;
                option.text = camera.name || `Cámara ${index + 1}`;
                cameraSelect.appendChild(option);
            });

            // Seleccionar la cámara trasera en móviles
            let backCamera = cameras.find(camera => camera.name.toLowerCase().includes('back'));
            let defaultCamera = backCamera ? backCamera.id : cameras[0].id;

            cameraSelect.value = defaultCamera;
            changeCamera(defaultCamera);

            // Cambiar cámara al seleccionar otra
            cameraSelect.addEventListener('change', function() {
                changeCamera(this.value);
            });
        } else {
            alert('No se encontró una cámara.');
        }
    }).catch(function(e) {
        console.error(e);
        alert('Error al acceder a la cámara.');
    });

    // Escuchar el escaneo del QR
    scanner.addListener('scan', function(content) {
        window.location.href = 'registrar_asistencia.php?dni=' + content;
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
