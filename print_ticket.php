<?php
include 'conexion.php';
session_start();

if (!isset($_GET['id'])) {
    die("❌ Error: No se especificó un pedido.");
}

$id_pedido = intval($_GET['id']);

// 🔹 Obtener pedido y cliente
$sqlPedido = "
SELECT p.id, p.total, p.estado, p.fecha, c.nombre AS cliente, c.correo
FROM pedidos p
JOIN clientes c ON p.id_cliente = c.id
WHERE p.id = ?";
$stmt = $conexion->prepare($sqlPedido);
$stmt->bind_param("i", $id_pedido);
$stmt->execute();
$pedido = $stmt->get_result()->fetch_assoc();

if (!$pedido) {
    die("❌ Pedido no encontrado.");
}

// 🔹 Obtener detalle
$sqlDetalle = "
SELECT pl.nombre, pl.imagen, dp.cantidad, dp.precio_unitario
FROM detalle_pedidos dp
JOIN platos pl ON dp.plato_id = pl.id
WHERE dp.pedido_id = ?";
$stmt2 = $conexion->prepare($sqlDetalle);
$stmt2->bind_param("i", $id_pedido);
$stmt2->execute();
$detalle = $stmt2->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Boleta #<?= $pedido['id'] ?> | Cloud Food</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="assets/site-logo.css">
<style>
@media print {
  button { display: none !important; }
  body { margin: 0; padding: 0; background: #fff; }
  .ticket {
    width: 100%;
    max-width: 210mm;
    height: 148mm; /* mitad de A4 */
    margin: 0 auto;
    border: none;
    box-shadow: none;
    page-break-after: avoid;
  }
}

body {
  font-family: 'Poppins', sans-serif;
  background: #f9f9f9;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  padding: 20px;
  margin: 0;
}

.ticket {
  width: 80%;
  max-width: 210mm;
  min-height: 148mm;
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 10px;
  padding: 25px;
  box-shadow: 0 0 12px rgba(0,0,0,0.1);
}

/* ---- Encabezado ---- */
.logo {
  text-align: center;
}
.logo img {
  width: 90px;
  height: 90px;
  border-radius: 50%;
  object-fit: cover;
  margin-bottom: 5px;
}
.title {
  text-align: center;
  color: #6a1b1b;
  font-size: 1.3rem;
  font-weight: bold;
  margin: 5px 0;
}
.sub {
  text-align: center;
  font-size: 0.85rem;
  color: #333;
  margin-bottom: 10px;
}
hr {
  border: none;
  border-top: 1px dashed #aaa;
  margin: 10px 0;
}

/* ---- Datos del cliente ---- */
.info {
  font-size: 0.9rem;
  line-height: 1.4;
}
.info p {
  margin: 3px 0;
}

/* ---- Tabla ---- */
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
}
th, td {
  padding: 8px 0;
  font-size: 0.9rem;
}
th {
  border-bottom: 1px solid #ccc;
  color: #6a1b1b;
  text-align: left;
}
td:last-child, th:last-child { text-align: right; }
.plato-img {
  width: 40px;
  height: 40px;
  border-radius: 6px;
  object-fit: cover;
  margin-right: 8px;
  vertical-align: middle;
  box-shadow: 0 0 4px rgba(0,0,0,0.2);
}

/* ---- Totales ---- */
.total {
  text-align: right;
  font-size: 1rem;
  font-weight: bold;
  margin-top: 10px;
  color: #000;
}

/* ---- QR ---- */
.qr {
  text-align: center;
  margin-top: 15px;
}
.qr img {
  width: 120px;
  height: 120px;
  border: 2px solid #6a1b1b;
  border-radius: 10px;
}
.qr small {
  display: block;
  margin-top: 5px;
  font-size: 0.85rem;
}

/* ---- Footer ---- */
.footer {
  text-align: center;
  font-size: 0.8rem;
  margin-top: 15px;
  color: #555;
}

/* ---- Botón ---- */
button {
  display: block;
  margin: 20px auto;
  background: linear-gradient(90deg, #6a1b1b, #b22222);
  color: #fff;
  border: none;
  padding: 10px 25px;
  border-radius: 25px;
  cursor: pointer;
  transition: 0.3s;
  font-size: 0.9rem;
}
button:hover {
  background: linear-gradient(90deg, #b22222, #6a1b1b);
  transform: scale(1.05);
}
</style>
</head>
<body>

<div class="ticket">
    <div class="logo">
    <img src="uploads/Logo cloud food en oscuro.png" alt="Cloud Food" onerror="this.src='https://cdn-icons-png.flaticon.com/512/3075/3075977.png'" class="site-logo site-logo--ticket" loading="lazy" decoding="async">
  </div>
  <div class="title">CLOUD FOOD</div>
  <div class="sub">RUC: 20598765432 • Jr. Principal 123 - Barranca<br>Tel: (01) 999-999-999</div>
  <hr>
  <div style="text-align:center;font-weight:bold;color:#6a1b1b;">BOLETA DE VENTA ELECTRÓNICA</div>
  <div style="text-align:center;font-size:0.9rem;">N° <?= str_pad($pedido['id'],6,"0",STR_PAD_LEFT) ?></div>
  <hr>

  <div class="info">
    <p><b>Cliente:</b> <?= htmlspecialchars($pedido['cliente']) ?></p>
    <p><b>Correo:</b> <?= htmlspecialchars($pedido['correo']) ?></p>
    <p><b>Fecha:</b> <?= date("d/m/Y H:i", strtotime($pedido['fecha'])) ?></p>
    <p><b>Estado:</b> <?= htmlspecialchars($pedido['estado']) ?></p>
  </div>

  <hr>
  <table>
    <tr>
      <th>Plato</th>
      <th>Cant</th>
      <th>Importe</th>
    </tr>
    <?php while ($i = $detalle->fetch_assoc()): ?>
    <tr>
      <td><img src="<?= htmlspecialchars($i['imagen']) ?>" class="plato-img" alt=""> <?= htmlspecialchars($i['nombre']) ?></td>
      <td><?= $i['cantidad'] ?></td>
      <td>S/ <?= number_format($i['cantidad'] * $i['precio_unitario'], 2) ?></td>
    </tr>
    <?php endwhile; ?>
  </table>

  <hr>
  <div class="total">TOTAL: S/ <?= number_format($pedido['total'], 2) ?></div>

  <div class="qr">
    <p><b>Escanea y paga con Yape 📱</b></p>
    <img src="QR_YAPE.jpeg" alt="QR de Yape" onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/4/4a/QR_code_example.svg'">
    <small>Yapea al <b>987 654 321</b></small>
  </div>

  <hr>
  <div class="footer">
    Gracias por tu preferencia 💛<br>
  Cloud Food © <?= date("Y") ?>
  </div>
</div>

<button onclick="window.print()"><i class="fa-solid fa-print"></i> Imprimir Boleta</button>

</body>
</html>
