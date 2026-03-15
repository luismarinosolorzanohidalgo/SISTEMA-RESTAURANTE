<?php
// pedidos_ajax.php
header('Content-Type: application/json; charset=utf-8');
include 'conexion.php';

// Traer pedidos activos (excluye rechazados/cancelados/entregados si quieres solo los vivos)
$sql = "SELECT p.id, p.total, p.estado, p.fecha, c.nombre AS cliente
        FROM pedidos p
        JOIN clientes c ON p.id_cliente = c.id
        ORDER BY p.id DESC";
$res = $conexion->query($sql);

$pedidos = [];
while ($row = $res->fetch_assoc()) {
    $pedidos[] = [
        "id"      => (int)$row['id'],
        "cliente" => $row['cliente'],
        "total"   => (float)$row['total'],
        "estado"  => $row['estado'],
        "fecha"   => $row['fecha'],
        // Opcional: vista previa de items (si tuvieras tabla pedido_items)
        "items"   => "" // aquí podrías poner resumen
    ];
}

echo json_encode($pedidos, JSON_UNESCAPED_UNICODE);
