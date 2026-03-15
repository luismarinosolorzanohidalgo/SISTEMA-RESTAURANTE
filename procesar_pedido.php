<?php
session_start();
include 'conexion.php';
header("Content-Type: application/json; charset=UTF-8");

// Validar sesión
if (!isset($_SESSION['cliente_id'])) {
    echo json_encode(["success"=>false, "message"=>"Sesión inválida."]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['cart']) || !is_array($input['cart']) || count($input['cart'])===0) {
    echo json_encode(["success"=>false, "message"=>"Carrito vacío o datos inválidos."]);
    exit;
}

$id_cliente = intval($_SESSION['cliente_id']);
$cart = $input['cart'];

// Validar cada item y calcular total usando la BD (para evitar manipulación de precio desde cliente)
$total = 0.0;
$items = []; // {plato_id, cantidad, precio_unitario}
$stmtPlato = $conexion->prepare("SELECT precio, nombre FROM platos WHERE id = ? LIMIT 1");

foreach ($cart as $c) {
    $id_plato = intval($c['id'] ?? 0);
    $cantidad = intval($c['cantidad'] ?? 0);
    if ($id_plato <= 0 || $cantidad <= 0) {
        continue;
    }
    $stmtPlato->bind_param("i", $id_plato);
    $stmtPlato->execute();
    $res = $stmtPlato->get_result();
    if ($res->num_rows === 0) {
        echo json_encode(["success"=>false, "message"=>"Plato con ID $id_plato no encontrado."]);
        exit;
    }
    $row = $res->fetch_assoc();
    $precio_unit = floatval($row['precio']);
    $sub = $precio_unit * $cantidad;
    $total += $sub;
    $items[] = [
        'plato_id' => $id_plato,
        'cantidad' => $cantidad,
        'precio_unitario' => $precio_unit,
        'nombre' => $row['nombre']
    ];
}

if (count($items) === 0) {
    echo json_encode(["success"=>false, "message"=>"No hay items válidos en el carrito."]);
    exit;
}

// Iniciar transacción
$conexion->begin_transaction();

try {
    // Insert pedido
    $stmtIns = $conexion->prepare("INSERT INTO pedidos (id_cliente, total, estado, fecha) VALUES (?, ?, 'Pendiente', NOW())");
    $stmtIns->bind_param("id", $id_cliente, $total);
    $stmtIns->execute();
    $id_pedido = $conexion->insert_id;

    // Insert detalle_pedidos
    $stmtDet = $conexion->prepare("INSERT INTO detalle_pedidos (pedido_id, plato_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
    foreach ($items as $it) {
        $stmtDet->bind_param("iiid", $id_pedido, $it['plato_id'], $it['cantidad'], $it['precio_unitario']);
        $stmtDet->execute();
    }

    $conexion->commit();

    echo json_encode(["success"=>true, "message"=>"Pedido registrado (ID #$id_pedido).", "pedido_id" => $id_pedido]);

} catch (Exception $e) {
    $conexion->rollback();
    echo json_encode(["success"=>false, "message"=>"Error al guardar pedido: " . $e->getMessage()]);
}
?>
