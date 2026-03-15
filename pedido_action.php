<?php
include 'conexion.php';
header('Content-Type: application/json; charset=utf-8');

// 🧩 Validar datos
if (empty($_POST['id']) || empty($_POST['accion'])) {
    echo json_encode(['success' => false, 'title' => 'Error', 'message' => 'Datos incompletos', 'icon' => 'error']);
    exit;
}

$id = intval($_POST['id']);
$accion = trim($_POST['accion']);

// 🧭 Determinar nuevo estado
switch ($accion) {
    case 'aceptar':
        $nuevoEstado = 'En preparación';
        $titulo = 'Pedido Aceptado';
        $mensaje = 'El pedido fue aceptado correctamente.';
        $icono = 'success';
        break;
    case 'rechazar':
        $nuevoEstado = 'Rechazado';
        $titulo = 'Pedido Rechazado';
        $mensaje = 'El pedido fue rechazado.';
        $icono = 'error';
        break;
    default:
        echo json_encode(['success' => false, 'title' => 'Error', 'message' => 'Acción inválida', 'icon' => 'error']);
        exit;
}

// 🧱 Actualizar base de datos
$stmt = $conexion->prepare("UPDATE pedidos SET estado=? WHERE id=?");
$stmt->bind_param("si", $nuevoEstado, $id);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'title' => $titulo,
        'message' => $mensaje,
        'icon' => $icono
    ]);
} else {
    echo json_encode([
        'success' => false,
        'title' => 'Error',
        'message' => 'No se pudo actualizar el pedido.',
        'icon' => 'error'
    ]);
}

$stmt->close();
$conexion->close();
?>
