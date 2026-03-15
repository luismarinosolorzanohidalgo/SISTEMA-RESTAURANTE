<?php
include 'conexion.php';

if (!isset($_GET['id'])) {
    die("❌ Error: ID de pedido no proporcionado.");
}

$id_pedido = intval($_GET['id']);

// 🔹 Obtener datos del pedido
$sql = "SELECT p.id, p.total, p.estado, p.fecha, c.nombre AS cliente
        FROM pedidos p
        JOIN clientes c ON p.id_cliente = c.id
        WHERE p.id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_pedido);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("<h2 style='color:red;text-align:center;margin-top:50px;'>❌ El pedido no existe.</h2>");
}
$pedido = $result->fetch_assoc();
$stmt->close();

// 🔹 Obtener detalle del pedido
$sql = "SELECT dp.cantidad, dp.precio_unitario, pl.nombre
        FROM detalle_pedidos dp
        JOIN platos pl ON dp.plato_id = pl.id
        WHERE dp.pedido_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_pedido);
$stmt->execute();
$detalle_result = $stmt->get_result();
$platos = [];
while ($fila = $detalle_result->fetch_assoc()) {
    $platos[] = $fila;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Pedido #<?= $pedido['id']; ?> | Cloud Food</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --gold: #ffe680;
            --wine: #6a1b1b;
            --bg-dark: #1c1c1c;
            --light: #fff;
        }

        body {
            background: linear-gradient(135deg, #1b1b1b, #2b2b2b);
            font-family: 'Segoe UI', Tahoma, sans-serif;
            color: var(--light);
            overflow-x: hidden;
        }

        h2 {
            text-align: center;
            font-size: 2.5rem;
            margin-top: 40px;
            background: linear-gradient(to right, var(--gold), var(--wine));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 0 15px rgba(255,230,128,0.4);
            animation: fadeDown 1.2s ease;
        }

        @keyframes fadeDown {
            from {opacity: 0; transform: translateY(-40px);}
            to {opacity: 1; transform: translateY(0);}
        }

        .card {
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.2);
            backdrop-filter: blur(15px);
            border-radius: 18px;
            box-shadow: 0 0 30px rgba(0,0,0,0.7);
            color: var(--gold);
            margin-bottom: 25px;
            animation: floatUp 1.3s ease;
        }

        @keyframes floatUp {
            from {opacity: 0; transform: translateY(40px);}
            to {opacity: 1; transform: translateY(0);}
        }

        .table {
            background: rgba(255,255,255,0.05);
            color: var(--light);
            border-radius: 10px;
            overflow: hidden;
        }

        .table th {
            background-color: var(--wine);
            color: var(--gold);
            text-align: center;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(255,230,128,0.1);
            transition: 0.3s;
        }

        .btn-cloudfood {
            background: linear-gradient(135deg, var(--gold), var(--wine));
            border: none;
            border-radius: 30px;
            color: white;
            font-weight: bold;
            padding: 12px 25px;
            font-size: 1rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 0 15px rgba(255,230,128,0.5);
        }

        .btn-cloudfood:hover {
            transform: scale(1.05);
            box-shadow: 0 0 30px rgba(255,230,128,0.7);
        }

        .footer {
            text-align: center;
            margin-top: 70px;
            color: rgba(255,255,255,0.6);
            font-size: 0.9rem;
        }

        /* Partículas */
        #particles {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: -1;
            pointer-events: none;
        }
    </style>
</head>
<body>
<canvas id="particles"></canvas>
<div class="container py-5">
    <h2>🧾 Detalle del Pedido #<?= $pedido['id']; ?></h2>

    <div class="card mt-5 p-4">
        <div class="card-body">
            <p><strong>👤 Cliente:</strong> <?= htmlspecialchars($pedido['cliente']); ?></p>
            <p><strong>📅 Fecha:</strong> <?= $pedido['fecha']; ?></p>
            <p><strong>📦 Estado:</strong> <span class="badge bg-warning text-dark px-3"><?= $pedido['estado']; ?></span></p>
            <p><strong>💵 Total:</strong> <span class="fw-bold">S/. <?= number_format($pedido['total'], 2); ?></span></p>
        </div>
    </div>

    <div class="text-center mt-5">
    <a href="panel_trabajador.php" class="btn-cloudfood"><i class="fa-solid fa-arrow-left"></i> Volver al Menú</a>
    </div>
</div>

<div class="footer">© <?= date("Y"); ?> Cloud Food — Sistema de Pedidos</div>

<script>
// Animación de partículas doradas
const canvas = document.getElementById("particles");
const ctx = canvas.getContext("2d");
canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

let particlesArray = [];
const numParticles = 80;

class Particle {
  constructor() {
    this.x = Math.random() * canvas.width;
    this.y = Math.random() * canvas.height;
    this.size = Math.random() * 3 + 1;
    this.speedX = (Math.random() - 0.5) * 0.5;
    this.speedY = (Math.random() - 0.5) * 0.5;
    this.color = "rgba(255,230,128,0.8)";
  }
  update() {
    this.x += this.speedX;
    this.y += this.speedY;
    if (this.x < 0 || this.x > canvas.width) this.speedX *= -1;
    if (this.y < 0 || this.y > canvas.height) this.speedY *= -1;
  }
  draw() {
    ctx.beginPath();
    ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
    ctx.fillStyle = this.color;
    ctx.fill();
  }
}

function initParticles() {
  particlesArray = [];
  for (let i = 0; i < numParticles; i++) {
    particlesArray.push(new Particle());
  }
}

function animateParticles() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  particlesArray.forEach(p => {
    p.update();
    p.draw();
  });
  requestAnimationFrame(animateParticles);
}

initParticles();
animateParticles();

window.addEventListener("resize", () => {
  canvas.width = window.innerWidth;
  canvas.height = window.innerHeight;
  initParticles();
});
</script>
</body>
</html>
