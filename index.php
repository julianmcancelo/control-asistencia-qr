<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instituto Beltrán - Control de Asistencia QR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to right, #1e3a8a, #4f46e5);
            color: white;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }
        .navbar {
            background: rgba(30, 58, 138, 0.9);
            backdrop-filter: blur(10px);
        }
        .btn-action {
            padding: 12px;
            font-size: 1.2rem;
            border-radius: 10px;
            transition: all 0.3s ease-in-out;
        }
        .btn-action:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>

<!-- Barra de Navegación Mejorada -->
<nav class="navbar navbar-expand-lg text-white py-3 shadow-lg">
    <div class="container">
        <a class="navbar-brand flex items-center gap-2 text-white font-bold text-lg" href="index.php">
            <img src="img/logo.png" alt="Logo" class="w-10 h-10">
            Instituto Beltrán - Asistencia QR
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse text-center" id="navbarNav">
            <ul class="navbar-nav ms-auto flex flex-col md:flex-row gap-4 text-lg">
                <li><a class="nav-link text-white hover:text-gray-300" href="registro.php">Registrar Usuario</a></li>
                <li><a class="nav-link text-white hover:text-gray-300" href="escanear_qr.php">Escanear QR</a></li>
                <li><a class="nav-link text-white hover:text-gray-300" href="historial.php">Historial</a></li>
                <li><a class="nav-link text-white hover:text-gray-300" href="generar_credencial.php">Generar Credencial</a></li>
                <li><a class="nav-link text-white hover:text-gray-300" href="configuracion.php">Configuración</a></li>
                <li><a class="nav-link text-white hover:text-gray-300" href="reportes.php">Reportes</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Contenido Principal -->
<div class="container text-center mt-5">
    <h1 class="text-4xl font-bold mb-4">Bienvenido al Sistema de Asistencia del Instituto Beltrán</h1>
    <p class="text-lg">Escanea tu código QR para registrar tu asistencia de manera rápida y segura.</p>

    <!-- Botones de Acciones -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mt-6">
        <a href="registro.php" class="btn-action bg-indigo-500 text-white shadow-md hover:bg-indigo-700 transition">Registrar Usuario</a>
        <a href="escanear_qr.php" class="btn-action bg-teal-500 text-white shadow-md hover:bg-teal-700 transition">Escanear QR</a>
        <a href="historial.php" class="btn-action bg-amber-500 text-white shadow-md hover:bg-amber-700 transition">Ver Historial</a>
        <a href="generar_credencial.php" class="btn-action bg-pink-500 text-white shadow-md hover:bg-pink-700 transition">Generar Credencial</a>
        <a href="configuracion.php" class="btn-action bg-gray-600 text-white shadow-md hover:bg-gray-800 transition">Configuración</a>
        <a href="reportes.php" class="btn-action bg-red-500 text-white shadow-md hover:bg-red-700 transition">Reportes</a>
    </div>
</div>

<!-- Pie de Página -->
<footer class="bg-indigo-900 text-white text-center py-4 mt-10">
    <p>&copy; <?php echo date("Y"); ?> Instituto Beltrán - Control de Asistencia QR | Desarrollado por Julian Manuel Cancelo</p>
</footer>

</body>
</html>
