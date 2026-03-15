<?php
// set_tiempo.php
include 'conexion.php';
header('Content-Type: application/json; charset=utf-8');

$id_pedido = isset($_POST['id_pedido']) ? intval($_POST['id_pedido']) : 0;
$tiempo_minutos = isset($_POST['tiempo_minutos']) ? intval($_POST['tiempo_minutos']) : 0;

if(!$id_pedido || !$tiempo_minutos){
    echo json_encode(['success'=>false,'message'=>'Parámetros inválidos']);
    exit;
}

// calcular tiempo de finalización (NOW + minutos)
$now = new DateTime('now', new DateTimeZone('UTC')); // usa UTC o tu zona
$now->modify("+{$tiempo_minutos} minutes");
$tiempo_fin = $now->format('Y-m-d H:i:s');

$stmt = $conexion->prepare("UPDATE pedidos SET tiempo_estimado_minutes = ?, tiempo_fin = ? WHERE id = ?");
if(!$stmt){
    echo json_encode(['success'=>false,'message'=>'Error interno DB (prepare).']);
    exit;
}
$stmt->bind_param("isi", $tiempo_minutos, $tiempo_fin, $id_pedido);
if($stmt->execute()){
    echo json_encode([
      'success' => true,
      'message' => "Tiempo estimado guardado ({$tiempo_minutos} min).",
      'tiempo_fin' => $tiempo_fin
    ]);
} else {
    echo json_encode(['success'=>false,'message'=>'No se pudo actualizar la base de datos.']);
}
$stmt->close();
$conexion->close();
?>
