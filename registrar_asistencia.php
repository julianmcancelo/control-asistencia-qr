<?php
require 'conexion.php';

if (isset($_GET['dni'])) {
    $dni = $_GET['dni'];

    // Buscar al usuario en la base de datos
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE dni = ?");
    $stmt->bind_param("s", $dni);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($usuario_id);
        $stmt->fetch();

        // Insertar asistencia
        $stmt = $conn->prepare("INSERT INTO asistencia (usuario_id) VALUES (?)");
        $stmt->bind_param("i", $usuario_id);
        if ($stmt->execute()) {
            echo "✅ Asistencia registrada correctamente.";
        } else {
            echo "❌ Error al registrar la asistencia.";
        }
    } else {
        echo "❌ Usuario no encontrado.";
    }
    $stmt->close();
} else {
    echo "❌ No se recibió el DNI.";
}
?>
