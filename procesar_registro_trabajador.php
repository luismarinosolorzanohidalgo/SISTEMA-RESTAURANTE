<?php
session_start();
header('Content-Type: application/json');
include 'conexion.php'; // Asegúrate de que este archivo exista y funcione
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';
    $sede = intval($_POST['sede'] ?? 0);
    // Validaciones básicas
    if (empty($nombre) || empty($correo) || empty($password) || $sede <= 0) {
        echo json_encode(["success" => false, "message" => "Todos los campos son obligatorios."]);
        exit;
    }
    if (strlen($password) < 6) {
        echo json_encode(["success" => false, "message" => "La contraseña debe tener al menos 6 caracteres."]);
        exit;
    }
    // Verificar si el correo ya existe en la tabla de trabajadores
    $stmt = $conexion->prepare("SELECT id FROM trabajadores WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Este correo ya está registrado como trabajador."]);
        $stmt->close();
        exit;
    }
    $stmt->close();
    // Hashear la contraseña de forma segura
    $hash = password_hash($password, PASSWORD_BCRYPT);
    // Insertar en la tabla 'trabajadores' (asegúrate de que la tabla exista con columnas: id, nombre, correo, password, sede_id)
    $stmt = $conexion->prepare("INSERT INTO trabajadores (nombre, correo, password, sede) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Error en la preparación de la consulta: " . $conexion->error]);
        exit;
    }
    $stmt->bind_param("sssi", $nombre, $correo, $hash, $sede);
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Registro exitoso. Redirigiendo al login..."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al registrar: " . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
}
$conexion->close();
?>
