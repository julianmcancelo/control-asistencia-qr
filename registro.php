<?php
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $dni = $_POST["dni"];
    $legajo = $_POST["legajo"];
    $turno = $_POST["turno"];
    $carrera = $_POST["carrera"];
    $email = $_POST["email"];
    $qr_url = "generar_qr.php?dni=" . urlencode($dni);

    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, dni, legajo, turno, carrera, email, qr) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $nombre, $dni, $legajo, $turno, $carrera, $email, $qr_url);

    if ($stmt->execute()) {
        $mensaje = "<div class='alert alert-success'>✅ Usuario registrado correctamente.</div>
                    <div class='text-center'><img src='$qr_url' alt='Código QR'></div>";
    } else {
        $mensaje = "<div class='alert alert-danger'>❌ Error al registrar usuario: " . $stmt->error . "</div>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow p-4">
        <h2 class="text-center">Registro de Usuario</h2>
        <?php if (isset($mensaje)) echo $mensaje; ?>
        <form action="registro.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Nombre:</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">DNI:</label>
                <input type="text" name="dni" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Legajo:</label>
                <input type="text" name="legajo" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Turno:</label>
                <select name="turno" class="form-control" required>
                    <option value="Mañana">Mañana</option>
                    <option value="Tarde">Tarde</option>
                    <option value="Noche">Noche</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Carrera:</label>
                <input type="text" name="carrera" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Correo Electrónico:</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Registrar y Generar QR</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
