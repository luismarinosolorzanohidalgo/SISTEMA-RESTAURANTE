<?php
include 'conexion.php';
$res = $conexion->query("SELECT p.id, c.nombre AS cliente, pl.nombre AS plato
FROM pedidos p
JOIN clientes c ON p.id_cliente=c.id
JOIN platos pl ON p.id_plato=pl.id
WHERE p.leido=0
ORDER BY p.fecha DESC");
$pedidos = [];
while($row = $res->fetch_assoc()){
    $pedidos[] = $row;
}
$conexion->query("UPDATE pedidos SET leido=1 WHERE leido=0");
echo json_encode($pedidos);
?>
