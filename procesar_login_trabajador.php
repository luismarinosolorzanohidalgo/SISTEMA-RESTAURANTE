<?php
include 'conexion.php';
session_start();
header("Content-Type: application/json");

$correo = trim($_POST['correo'] ?? '');
$password = $_POST['password'] ?? '';
$sede = $_POST['sede'] ?? '';

if (!$correo || !$password || !$sede) {
    echo json_encode(["success" => false, "message" => "Faltan datos."]);
    exit;
}

// Buscar trabajador en la base
$stmt = $conexion->prepare("SELECT * FROM trabajadores WHERE correo = ?");
$stmt->bind_param("s", $correo);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $trabajador = $result->fetch_assoc();

    // --- Verificar la sede ---
    if ($trabajador['sede'] != $sede) {
        echo json_encode(["success" => false, "message" => "No perteneces a esta sede."]);
        exit;
    }

    // --- Verificar contraseña ---
    $password_valida = false;

    // Caso 1: password_hash()
    if (password_verify($password, $trabajador['password'])) {
        $password_valida = true;
    }
    // Caso 2: md5 (por compatibilidad con registros antiguos)
    elseif (md5($password) === $trabajador['password']) {
        $password_valida = true;
    }

    if ($password_valida) {
        // --- Crear sesión ---
        $_SESSION['user_id'] = $trabajador['id'];
        $_SESSION['nombre'] = $trabajador['nombre'];
        $_SESSION['rol'] = $trabajador['rol'] ?? 'Trabajador';
        $_SESSION['sede'] = $trabajador['sede'];

        echo json_encode([
            "success" => true,
            "message" => "Inicio de sesión exitoso",
            "nombre" => $trabajador['nombre'],
            "rol" => $_SESSION['rol']
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Contraseña incorrecta."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "No eres un trabajador registrado."]);
}

$stmt->close();
$conexion->close();
?>
