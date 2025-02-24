<?php
require 'conexion.php';

$condiciones = [];
if (isset($_GET['carrera']) && !empty($_GET['carrera'])) {
    $condiciones[] = "carrera = '" . $conn->real_escape_string($_GET['carrera']) . "'";
}
if (isset($_GET['turno']) && !empty($_GET['turno'])) {
    $condiciones[] = "turno = '" . $conn->real_escape_string($_GET['turno']) . "'";
}
if (isset($_GET['fecha']) && !empty($_GET['fecha'])) {
    $condiciones[] = "DATE(fecha_hora) = '" . $conn->real_escape_string($_GET['fecha']) . "'";
}
$where = count($condiciones) ? "WHERE " . implode(" AND ", $condiciones) : "";
$query = "SELECT * FROM asistencias $where ORDER BY fecha_hora DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Accesos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>📋 Historial de Accesos</h2>

    <!-- Filtros -->
    <form method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <input type="date" name="fecha" class="form-control" value="<?= $_GET['fecha'] ?? '' ?>">
            </div>
            <div class="col-md-3">
                <select name="turno" class="form-control">
                    <option value="">Todos los Turnos</option>
                    <option value="Mañana" <?= (isset($_GET['turno']) && $_GET['turno'] == 'Mañana') ? 'selected' : '' ?>>Mañana</option>
                    <option value="Tarde" <?= (isset($_GET['turno']) && $_GET['turno'] == 'Tarde') ? 'selected' : '' ?>>Tarde</option>
                    <option value="Noche" <?= (isset($_GET['turno']) && $_GET['turno'] == 'Noche') ? 'selected' : '' ?>>Noche</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="carrera" class="form-control" placeholder="Carrera" value="<?= $_GET['carrera'] ?? '' ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">🔍 Filtrar</button>
            </div>
        </div>
    </form>

    <!-- Tabla de asistencias -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>DNI</th>
                <th>Nombre</th>
                <th>Legajo</th>
                <th>Turno</th>
                <th>Carrera</th>
                <th>Tipo de Acceso</th>
                <th>Fecha y Hora</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['dni'] ?></td>
                    <td><?= $row['nombre'] ?></td>
                    <td><?= $row['legajo'] ?></td>
                    <td><?= $row['turno'] ?></td>
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
</body>
</html>
