<?php
session_start();
include 'conexion.php';

header("Content-Type: application/json; charset=UTF-8");

$correo = trim($_POST['correo'] ?? '');
$password = trim($_POST['password'] ?? '');
$sede_id = intval($_POST['sede'] ?? 0);

if (empty($correo) || empty($password) || empty($sede_id)) {
    echo json_encode(["success" => false, "message" => "⚠️ Completa todos los campos."]);
    exit;
}

// 🔹 Prevención de fuerza bruta
if (!isset($_SESSION['intentos_cliente'])) {
    $_SESSION['intentos_cliente'] = 0;
    $_SESSION['ultimo_intento'] = time();
}

if ($_SESSION['intentos_cliente'] >= 3) {
    $espera = 60 - (time() - $_SESSION['ultimo_intento']);
    if ($espera > 0) {
        echo json_encode([
            "success" => false,
            "message" => "🚫 Demasiados intentos. Espera $espera segundos."
        ]);
        exit;
    } else {
        $_SESSION['intentos_cliente'] = 0;
    }
}

try {
    $query = $conexion->prepare("SELECT id, nombre, correo, password, sede_id FROM clientes WHERE correo = ?");
    $query->bind_param("s", $correo);
    $query->execute();
    $res = $query->get_result();

    if ($res->num_rows === 0) {
        $_SESSION['intentos_cliente']++;
        $_SESSION['ultimo_intento'] = time();
        echo json_encode(["success" => false, "message" => "❌ Correo no registrado."]);
        exit;
    }

    $cliente = $res->fetch_assoc();

    // 🔒 Verificar sede y contraseña
    if ($cliente['sede_id'] != $sede_id) {
        echo json_encode(["success" => false, "message" => "🏢 La sede seleccionada no corresponde a tu cuenta."]);
        exit;
    }

    if (password_verify($password, $cliente['password'])) {
        // Guardar sesión
        $_SESSION['cliente_id'] = $cliente['id'];
        $_SESSION['nombre'] = $cliente['nombre'];
        $_SESSION['rol'] = 'Cliente';
        $_SESSION['intentos_cliente'] = 0;

        echo json_encode([
            "success" => true,
            "message" => "✅ Bienvenido, {$cliente['nombre']}.",
            "nombre" => $cliente['nombre']
        ]);
    } else {
        $_SESSION['intentos_cliente']++;
        $_SESSION['ultimo_intento'] = time();
        echo json_encode(["success" => false, "message" => "⚠️ Contraseña incorrecta."]);
    }

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "⚠️ Error interno: " . $e->getMessage()]);
}
?>
