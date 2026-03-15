<?php
include 'conexion.php';
header('Content-Type: application/json; charset=utf-8');

// 🧩 Obtener y validar datos
$id      = isset($_POST['id']) ? intval($_POST['id']) : 0;
$estado  = trim($_POST['estado'] ?? '');
$tiempo  = trim($_POST['tiempo'] ?? ''); // Nuevo campo opcional

$allowed = ['Pendiente', 'En preparación', 'En camino', 'Entregado', 'Cancelado', 'Rechazado'];

if (!$id || (!in_array($estado, $allowed) && $tiempo === '')) {
    echo json_encode([
        'success' => false,
        'title' => 'Error',
        'message' => 'Parámetros inválidos',
        'icon' => 'error'
    ]);
    exit;
}

// 🧱 Si solo se actualiza el tiempo estimado
if ($tiempo !== '' && $estado === '') {
    $stmt = $conexion->prepare("UPDATE pedidos SET tiempo_estimado=? WHERE id=?");
    $stmt->bind_param("si", $tiempo, $id);
} 
// 🧱 Si se actualiza estado (y opcionalmente también el tiempo)
elseif ($tiempo !== '') {
    $stmt = $conexion->prepare("UPDATE pedidos SET estado=?, tiempo_estimado=? WHERE id=?");
    $stmt->bind_param("ssi", $estado, $tiempo, $id);
} 
// 🧱 Si solo se actualiza estado
else {
    $stmt = $conexion->prepare("UPDATE pedidos SET estado=? WHERE id=?");
    $stmt->bind_param("si", $estado, $id);
}

// 🚀 Ejecutar actualización
if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'title' => 'Actualizado correctamente',
        'message' => $tiempo !== '' 
            ? 'Tiempo estimado y/o estado actualizados con éxito.' 
            : 'El pedido fue marcado como "' . $estado . '".',
        'icon' => 'success'
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
