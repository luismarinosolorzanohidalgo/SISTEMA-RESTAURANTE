<?php
include 'conexion.php';
header('Content-Type: application/json; charset=utf-8');

// --- Filtros recibidos desde el front
$filtroPedidos  = $_GET['filtroPedidos']  ?? 'dia';
$filtroClientes = $_GET['filtroClientes'] ?? 'mes';

// --- Función para armar query según filtro
function filtroQuery($campoFecha, $filtro) {
    switch ($filtro) {
        case 'dia':
            return "DATE($campoFecha) AS periodo";
        case 'semana':
            return "YEARWEEK($campoFecha, 1) AS periodo";
        case 'mes':
            return "DATE_FORMAT($campoFecha, '%Y-%m') AS periodo";
        case 'anio':
            return "YEAR($campoFecha) AS periodo";
        default:
            return "DATE($campoFecha) AS periodo";
    }
}

// 📈 Pedidos
$pedidos = [];
$periodoPedidos = filtroQuery("fecha", $filtroPedidos);
$res = $conexion->query("
    SELECT $periodoPedidos, COUNT(*) as total 
    FROM pedidos 
    GROUP BY periodo
    ORDER BY periodo
");
while ($row = $res->fetch_assoc()) {
    $pedidos[] = $row;
}

// 📊 Clientes
$clientes = [];
$periodoClientes = filtroQuery("fecha_registro", $filtroClientes);
$res = $conexion->query("
    SELECT $periodoClientes, COUNT(*) as total 
    FROM clientes 
    GROUP BY periodo
    ORDER BY periodo
");
while ($row = $res->fetch_assoc()) {
    $clientes[] = $row;
}

// 🥧 Trabajadores
$trabajadores = [];
$res = $conexion->query("
    SELECT rol, COUNT(*) as total 
    FROM trabajadores 
    GROUP BY rol
");
while ($row = $res->fetch_assoc()) {
    $trabajadores[] = $row;
}

// 📜 Historial diario (para el modal)
$historial = [];
$res = $conexion->query("
    SELECT 
        DATE(fecha) AS fecha,
        (SELECT COUNT(*) FROM pedidos p WHERE DATE(p.fecha) = DATE(f.fecha)) AS pedidos,
        (SELECT COUNT(*) FROM clientes c WHERE DATE(c.fecha_registro) = DATE(f.fecha)) AS clientes
    FROM (
        SELECT fecha FROM pedidos
        UNION
        SELECT fecha_registro FROM clientes
    ) f
    GROUP BY DATE(fecha)
    ORDER BY fecha ASC
");
while ($row = $res->fetch_assoc()) {
    $historial[] = $row;
}

// --- Respuesta JSON
echo json_encode([
    "pedidos"      => $pedidos,
    "clientes"     => $clientes,
    "trabajadores" => $trabajadores,
    "historial"    => $historial
], JSON_UNESCAPED_UNICODE);
