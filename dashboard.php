<?php
require 'conexion.php';

// Obtener totales
$total_usuarios = $conn->query("SELECT COUNT(*) as total FROM usuarios")->fetch_assoc()['total'];
$total_asistencias = $conn->query("SELECT COUNT(*) as total FROM asistencias")->fetch_assoc()['total'];
$ultimos_accesos = $conn->query("SELECT * FROM asistencias ORDER BY fecha_hora DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Control de Asistencias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #f4f6f9;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #343a40;
            color: white;
            position: fixed;
            padding: 20px;
            transition: all 0.3s;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .sidebar a:hover {
            background: #495057;
        }
        .content {
            margin-left: 270px;
            padding: 20px;
        }
        .dashboard-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .dashboard-card h3 {
            font-size: 18px;
            color: #007bff;
        }
        .stats {
            font-size: 28px;
            font-weight: bold;
            color: #28a745;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h3 class="text-center">📊 Dashboard</h3>
    <a href="dashboard.php">🏠 Inicio</a>
    <a href="registro.php">👤 Registrar Usuario</a>
    <a href="escanear_qr.php">📷 Escanear QR</a>
    <a href="historial.php">📜 Historial de Accesos</a>
    <a href="configuracion.php">⚙️ Configuración</a>
</div>

<!-- Contenido Principal -->
<div class="content">
    <h2>📊 Panel de Control</h2>

    <!-- Dashboard -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="dashboard-card">
                <h3>Usuarios Registrados</h3>
                <p id="total_usuarios" class="stats"><?= $total_usuarios ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card">
                <h3>Accesos Registrados</h3>
                <p id="total_asistencias" class="stats"><?= $total_asistencias ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card">
                <h3>Accesos Hoy</h3>
                <p id="accesos_hoy" class="stats">0</p>
            </div>
        </div>
    </div>

    <!-- Gráfico -->
    <div class="mt-4">
        <h3>📊 Estadísticas de Asistencia</h3>
        <canvas id="chartAsistencia"></canvas>
    </div>

    <!-- Últimos accesos -->
    <div class="mt-4">
        <h3>🕒 Últimos Accesos</h3>
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Legajo</th>
                    <th>Carrera</th>
                    <th>Acceso</th>
                    <th>Fecha y Hora</th>
                </tr>
            </thead>
            <tbody id="ultimos_accesos">
                <?php while ($row = $ultimos_accesos->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['nombre'] ?></td>
                        <td><?= $row['legajo'] ?></td>
                        <td><?= $row['carrera'] ?></td>
                        <td class="<?= ($row['tipo_acceso'] == 'Entrada') ? 'text-success' : 'text-danger' ?>">
                            <?= $row['tipo_acceso'] ?>
                        </td>
                        <td><?= $row['fecha_hora'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function actualizarGrafico() {
        $.ajax({
            url: 'obtener_estadisticas.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                let ctx = document.getElementById('chartAsistencia').getContext('2d');
                if (window.miGrafico) { window.miGrafico.destroy(); }
                window.miGrafico = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.carreras,
                        datasets: [{
                            label: 'Asistencias por Carrera',
                            data: data.cantidad,
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: { y: { beginAtZero: true } }
                    }
                });
            }
        });
    }

    setInterval(() => {
        $.ajax({
            url: 'actualizar_dashboard.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                $("#total_usuarios").text(data.total_usuarios);
                $("#total_asistencias").text(data.total_asistencias);
                $("#accesos_hoy").text(data.accesos_hoy);
                $("#ultimos_accesos").html(data.ultimos_accesos);
            }
        });
    }, 5000);

    actualizarGrafico();
    setInterval(actualizarGrafico, 10000);
</script>

</body>
</html>
