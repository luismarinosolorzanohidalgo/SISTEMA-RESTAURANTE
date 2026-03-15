<?php
include 'conexion.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $password = trim($_POST['password']);
    $sede = intval($_POST['sede']);

    // Validar campos
    if (empty($nombre) || empty($correo) || empty($password) || empty($sede)) {
        echo json_encode(["success" => false, "message" => "Todos los campos son obligatorios"]);
        exit;
    }

    // Verificar si el correo ya está registrado
    $stmt = $conexion->prepare("SELECT id FROM clientes WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "El correo ya está registrado"]);
        exit;
    }
    $stmt->close();

    // Encriptar la contraseña
    $hash = password_hash($password, PASSWORD_BCRYPT);

    // Insertar cliente
    $stmt = $conexion->prepare("INSERT INTO clientes (nombre, correo, password, sede_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $nombre, $correo, $hash, $sede);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Registro exitoso. Ya puedes iniciar sesión"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al registrar: " . $conexion->error]);
    }

    $stmt->close();
    $conexion->close();
}
?>
