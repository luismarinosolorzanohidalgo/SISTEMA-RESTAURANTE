<?php
session_start();
include "conexion.php";

// ✅ Verificar conexión
if (!isset($conexion) || $conexion->connect_error) {
  die("Error de conexión con la base de datos.");
}

// ✅ Verificar estado del restaurante
$consultaEstado = $conexion->query("SELECT disponible FROM restaurantes WHERE id = 1");
$estado = 1; // por defecto activo
if ($consultaEstado && $fila = $consultaEstado->fetch_assoc()) {
  $estado = $fila['disponible'];
}

// ✅ Acceso solo para clientes
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Cliente') {
  header("Location: login_cliente.php");
  exit;
}

$id_cliente = intval($_SESSION['cliente_id'] ?? 0);
$nombre = htmlspecialchars($_SESSION['nombre'] ?? 'Cliente', ENT_QUOTES, 'UTF-8');

// ✅ Obtener platos disponibles
$sqlPlatos = "SELECT * FROM platos WHERE estado = 'Disponible' ORDER BY categoria, nombre";
$resPlatos = $conexion->query($sqlPlatos);
$menu = [];
if ($resPlatos && $resPlatos->num_rows > 0) {
  while ($r = $resPlatos->fetch_assoc()) {
    $cat = $r['categoria'] ?: 'Varios';
    $menu[$cat][] = $r;
  }
}

// ✅ Historial de pedidos del cliente
$sqlPedidos = "SELECT id, total, estado, fecha FROM pedidos WHERE id_cliente = $id_cliente ORDER BY fecha DESC LIMIT 20";
$pedidos = $conexion->query($sqlPedidos);
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Panel Cliente — Cloud Food</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="assets/site-logo.css">

  <style>
    :root {
      --gold: #ffd75f;
      --wine: #6a1b1b;
      --bg: #0d0d0d;
      --card: rgba(255, 255, 255, 0.04);
      --glass: rgba(255, 255, 255, 0.03);
      --text: #fff;
      --shadow: 0 8px 30px rgba(0, 0, 0, 0.6);
    }

    * {
      box-sizing: border-box
    }

    /* 🔥 Overlay de restaurante cerrado */
    #overlay-cerrado {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.95);
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      z-index: 9999;
      color: #fff;
      text-align: center;
      padding: 30px;
    }

    #overlay-cerrado h1 {
      font-size: 2.5rem;
      color: #ffd75f;
      margin-bottom: 15px;
      animation: glow 1.5s ease-in-out infinite alternate;
    }

    #overlay-cerrado p {
      font-size: 1.1rem;
      color: #ddd;
      max-width: 500px;
      margin: 0 auto 25px;
    }

    #overlay-cerrado i {
      font-size: 5rem;
      color: #ff3b3b;
      margin-bottom: 20px;
      animation: pulse 1.5s ease-in-out infinite;
    }

    #overlay-cerrado small {
      color: #aaa;
      margin-top: 25px;
      display: block;
    }


    @keyframes pulse {

      0%,
      100% {
        transform: scale(1);
        opacity: 0.7;
      }

      50% {
        transform: scale(1.1);
        opacity: 1;
      }
    }

    @keyframes glow {
      from {
        text-shadow: 0 0 10px #ffd75f, 0 0 20px #b8860b;
      }

      to {
        text-shadow: 0 0 25px #ffecb3, 0 0 35px #ffd75f;
      }
    }

    body {
      margin: 0;
      font-family: Inter, Poppins, system-ui;
      background: linear-gradient(135deg, var(--bg), #121212);
      color: var(--text);
    }

    /* Header */
    .header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 18px 28px;
      background: linear-gradient(90deg, var(--wine), #b22222);
      box-shadow: var(--shadow);
      position: sticky;
      top: 0;
      z-index: 50;
    }

    .logo {
      display: flex;
      align-items: center;
      gap: 12px;
      font-weight: 700;
      color: var(--gold);
      font-size: 1.2rem
    }

    .header .actions button {
      margin-left: 10px;
      padding: 9px 14px;
      border-radius: 999px;
      border: 2px solid var(--gold);
      background: transparent;
      color: var(--gold);
      cursor: pointer
    }

    .header .actions button:hover {
      background: var(--gold);
      color: var(--wine);
      transform: translateY(-3px);
      box-shadow: 0 0 12px rgba(255, 215, 95, 0.4)
    }

    /* Layout */
    .container {
      display: grid;
      grid-template-columns: 1fr 360px;
      gap: 28px;
      padding: 28px;
      align-items: start;
    }

    @media (max-width:980px) {
      .container {
        grid-template-columns: 1fr;
        padding: 18px
      }

      .cart-panel {
        position: static;
        width: auto;
        margin-top: 18px
      }
    }

    /* Left: menu + categories */
    .left {}

    .category {
      margin-bottom: 28px;
      background: linear-gradient(180deg, rgba(255, 255, 255, 0.02), rgba(255, 255, 255, 0.01));
      border-radius: 12px;
      padding: 18px;
      border: 1px solid rgba(255, 255, 255, 0.04);
    }

    .category h3 {
      margin: 0 0 12px;
      color: var(--gold);
      font-size: 1.1rem;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
      gap: 16px;
    }

    /* Card */
    .card {
      background: var(--card);
      border-radius: 12px;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      box-shadow: 0 8px 18px rgba(0, 0, 0, 0.55);
      transition: transform .22s ease, box-shadow .22s ease;
    }

    .card:hover {
      transform: translateY(-6px);
      box-shadow: 0 12px 30px rgba(255, 215, 95, 0.08);
    }

    .card img {
      width: 100%;
      height: 150px;
      object-fit: cover;
      display: block;
    }

    .card .meta {
      padding: 12px
    }

    .card h4 {
      margin: 0 0 6px;
      color: var(--gold);
      font-size: 1rem
    }

    .card p {
      margin: 0;
      color: #ccc;
      font-size: 0.88rem;
      min-height: 36px
    }

    .card .bottom {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 12px;
      border-top: 1px solid rgba(255, 255, 255, 0.02)
    }

    .price {
      font-weight: 700
    }

    .btn-add {
      background: linear-gradient(135deg, var(--gold), var(--wine));
      border: none;
      color: #111;
      padding: 8px 12px;
      border-radius: 999px;
      cursor: pointer;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: transform .15s ease;
    }

    .btn-add:hover {
      transform: scale(1.03)
    }

    /* Right: cart panel */
    .cart-panel {
      width: 360px;
      background: linear-gradient(180deg, rgba(255, 255, 255, 0.02), rgba(255, 255, 255, 0.01));
      border-radius: 12px;
      padding: 16px;
      border: 1px solid rgba(255, 255, 255, 0.04);
      position: sticky;
      top: 86px;
      height: fit-content;
    }

    .cart-panel h4 {
      margin: 0 0 12px;
      color: var(--gold)
    }

    .cart-list {
      max-height: 420px;
      overflow: auto;
      margin-bottom: 12px;
    }

    .btn-volver {
      display: inline-block;
      background: linear-gradient(90deg, #d4af37, #f7e27b);
      color: #000;
      font-weight: 600;
      text-decoration: none;
      padding: 12px 28px;
      border-radius: 50px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
      transition: all 0.3s ease;
    }

    .btn-volver:hover {
      background: linear-gradient(90deg, #f1d36b, #fff3b0);
      transform: scale(1.05);
      color: #222;
    }

    .cart-item {
      display: flex;
      gap: 10px;
      align-items: center;
      padding: 8px 6px;
      border-radius: 8px;
      margin-bottom: 8px;
      background: rgba(0, 0, 0, 0.15)
    }

    .cart-item img {
      width: 56px;
      height: 46px;
      object-fit: cover;
      border-radius: 8px
    }

    .cart-item .info {
      flex: 1
    }

    .cart-item .info b {
      display: block
    }

    .cart-item small {
      color: #ddd
    }

    .qty-controls {
      display: flex;
      gap: 6px;
      align-items: center
    }

    .icon-btn {
      background: transparent;
      border: 1px solid rgba(255, 255, 255, 0.06);
      border-radius: 8px;
      padding: 6px;
      cursor: pointer;
      color: var(--gold)
    }

    /* checkout */
    .total-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin: 12px 0;
      font-weight: 800;
      font-size: 1.05rem;
      color: var(--gold)
    }

    .checkout-btn {
      width: 100%;
      padding: 12px;
      border-radius: 10px;
      border: none;
      background: linear-gradient(135deg, var(--gold), var(--wine));
      color: #111;
      font-weight: 800;
      cursor: pointer;
    }

    .checkout-btn:disabled {
      opacity: 0.6;
      cursor: not-allowed
    }

    /* Historial (sección inferior) */
    .history-table {
      margin-top: 18px;
      width: 100%;
      border-collapse: collapse;
      font-size: 0.95rem
    }

    .history-table th,
    .history-table td {
      text-align: left;
      padding: 10px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.04)
    }

    .history-table th {
      color: var(--gold)
    }

    /* small helpers */
    .empty {
      color: #bbb;
      padding: 12px;
      text-align: center
    }
  </style>
</head>

<body>

  <div class="header">
    <div class="logo"><img src="uploads/Logo_cloud_food_en_oscuro-transparente (1).png" alt="Cloud Food" class="site-logo site-logo--header" loading="lazy" decoding="async"></div>
    <div style="display:flex;align-items:center;gap:12px" class="actions">
      <div style="color:#fff;font-weight:600">Hola, <?= htmlspecialchars($nombre) ?></div>
      
      <button onclick="document.getElementById('menu').scrollIntoView({ behavior: 'smooth' })">
        <i class="fa-solid fa-book-open"></i> Menú
      </button>
      <button onclick="window.location.href='perfil.php'">
  <i class="fa-solid fa-book-open"></i> Perfil
</button>

      <button onclick="document.getElementById('historial').scrollIntoView({ behavior: 'smooth' })">
        <i class="fa-solid fa-receipt"></i> Mis Pedidos
      </button>

      <button onclick="cerrarSesion()"><i class="fa-solid fa-right-from-bracket"></i> Salir</button>
    </div>
  </div>

  <div class="container">
    <!-- LEFT: MENU -->
    <div class="left">
      <section id="menu">
        <h2 style="color:var(--gold);margin:0 0 12px"><i class="fa-solid fa-bowl-food"></i> Menú</h2>

        <?php if (empty($menu)): ?>
          <div class="empty">No hay platos disponibles.</div>
        <?php endif; ?>

        <?php foreach ($menu as $categoria => $platos_cat): ?>
          <div class="category">
            <h3><i class="fa-solid fa-layer-group"></i> <?= htmlspecialchars($categoria) ?></h3>
            <div class="grid">
              <?php foreach ($platos_cat as $pl): ?>
                <div class="card" data-id="<?= $pl['id'] ?>" data-nombre="<?= htmlspecialchars($pl['nombre'], ENT_QUOTES) ?>" data-precio="<?= $pl['precio'] ?>" data-img="<?= htmlspecialchars($pl['imagen'], ENT_QUOTES) ?>">
                  <img src="<?= htmlspecialchars($pl['imagen']) ?>" alt="<?= htmlspecialchars($pl['nombre']) ?>">
                  <div class="meta">
                    <h4><?= htmlspecialchars($pl['nombre']) ?></h4>
                    <p><?= htmlspecialchars($pl['descripcion'] ?: 'Delicioso & fresco') ?></p>
                  </div>
                  <div class="bottom">
                    <div class="price">S/ <?= number_format($pl['precio'], 2) ?></div>
                    <button class="btn-add" onclick="addToCart(<?= $pl['id'] ?>, '<?= htmlspecialchars($pl['nombre'], ENT_QUOTES) ?>', <?= $pl['precio'] ?>, '<?= htmlspecialchars($pl['imagen'], ENT_QUOTES) ?>')">
                      <i class="fa-solid fa-cart-plus"></i> Añadir
                    </button>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>

      </section>
    </div>

    <!-- RIGHT: CART -->
    <aside class="cart-panel">
      <h4><i class="fa-solid fa-shopping-cart"></i> Tu carrito</h4>
      <div id="cartList" class="cart-list">
        <div class="empty">Tu carrito está vacío. Añade platos 😋</div>
      </div>

      <div id="totals" style="display:none">
        <div class="total-row"><span>Total</span> <span id="cartTotal">S/ 0.00</span></div>
        <button id="checkoutBtn" class="checkout-btn" onclick="checkout()" disabled>Confirmar pedido</button>
      </div>
      <small style="display:block;margin-top:8px;color:#bbb">Nota: Al confirmar, tu pedido se registrará como <b>Pendiente</b>.</small>
    </aside>
  </div>

  <!-- HISTORIAL -->
  <div style="padding: 0 28px 48px;">
    <section id="historial" style="margin-top:22px">
      <h2 style="color:var(--gold)"><i class="fa-solid fa-clock-rotate-left"></i> Historial de Pedidos</h2>

      <?php if ($pedidos && $pedidos->num_rows > 0): ?>
        <table class="history-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Total (S/)</th>
              <th>Estado</th>
              <th>Fecha</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($p = $pedidos->fetch_assoc()): ?>
              <tr>
                <td>#<?= $p['id'] ?></td>
                <td><?= number_format($p['total'], 2) ?></td>
                <td><?= htmlspecialchars($p['estado']) ?></td>
                <td><?= date("d/m/Y H:i", strtotime($p['fecha'])) ?></td>
                <td>
                  <button style="padding:7px 10px;border-radius:8px;border:1px solid rgba(255,255,255,0.06);background:transparent;color:var(--gold);cursor:pointer"
                    onclick="window.open('print_ticket.php?id=<?= $p['id'] ?>','_blank')">
                    <i class="fa-solid fa-print"></i> Imprimir
                  </button>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <div class="empty">Aún no tienes pedidos. ¡Anímate a pedir algo! 🍔</div>
      <?php endif; ?>
    </section>
  </div>

  <script>
    /* Carrito en memoria (frontend) */
    let cart = []; // {id, nombre, precio, cantidad, img}

    function findInCart(id) {
      return cart.find(i => i.id === id);
    }

    function addToCart(id, nombre, precio, img) {
      const existing = findInCart(id);
      if (existing) {
        existing.cantidad += 1;
      } else {
        cart.push({
          id: Number(id),
          nombre: nombre,
          precio: Number(precio),
          cantidad: 1,
          img: img
        });
      }
      renderCart();
    }

    /* Render cart */
    function renderCart() {
      const list = document.getElementById('cartList');
      list.innerHTML = '';
      if (cart.length === 0) {
        list.innerHTML = '<div class="empty">Tu carrito está vacío. Añade platos 😋</div>';
        document.getElementById('totals').style.display = 'none';
        document.getElementById('checkoutBtn').disabled = true;
        return;
      }
      document.getElementById('totals').style.display = 'block';
      document.getElementById('checkoutBtn').disabled = false;

      cart.forEach(item => {
        const div = document.createElement('div');
        div.className = 'cart-item';
        div.innerHTML = `
      <img src="${item.img}" alt="">
      <div class="info"><b>${item.nombre}</b><small>S/ ${item.precio.toFixed(2)}</small></div>
      <div style="display:flex;flex-direction:column;align-items:flex-end">
        <div class="qty-controls">
          <button class="icon-btn" onclick="changeQty(${item.id}, -1)">-</button>
          <div style="padding:0 8px">${item.cantidad}</div>
          <button class="icon-btn" onclick="changeQty(${item.id}, 1)">+</button>
        </div>
        <div style="margin-top:6px;color:#ddd">S/ ${(item.precio * item.cantidad).toFixed(2)}</div>
        <button style="margin-top:8px;background:transparent;border:none;color:#f66;cursor:pointer" onclick="removeItem(${item.id})">Eliminar</button>
      </div>
    `;
        list.appendChild(div);
      });

      const total = cart.reduce((s, i) => s + (i.precio * i.cantidad), 0);
      document.getElementById('cartTotal').innerText = 'S/ ' + total.toFixed(2);
    }

    /* Quantity change */
    function changeQty(id, delta) {
      const item = findInCart(id);
      if (!item) return;
      item.cantidad += delta;
      if (item.cantidad <= 0) cart = cart.filter(i => i.id !== id);
      renderCart();
    }

    function removeItem(id) {
      cart = cart.filter(i => i.id !== id);
      renderCart();
    }

    /* Checkout: envia carrito al servidor (JSON) */
    function checkout() {
      if (cart.length === 0) return;
      // confirm details
      const lines = cart.map(i => `${i.cantidad} × ${i.nombre} — S/ ${ (i.precio * i.cantidad).toFixed(2) }`).join('<br>');
      Swal.fire({
        title: 'Confirmar pedido',
        html: `<div style="text-align:left">${lines}<hr><b>Total: S/ ${ cart.reduce((s,i)=> s + i.precio*i.cantidad,0).toFixed(2) }</b></div>`,
        showCancelButton: true,
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#6a1b1b'
      }).then(result => {
        if (result.isConfirmed) {
          // enviar por fetch
          const payload = {
            cart: cart
          };
          fetch('procesar_pedido.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json'
              },
              body: JSON.stringify(payload)
            })
            .then(r => r.json())
            .then(data => {
              if (data.success) {
                Swal.fire('✅ Pedido registrado', data.message, 'success');
                cart = [];
                renderCart();
                // recargar historial para que aparezca el pedido
                setTimeout(() => location.reload(), 1200);
              } else {
                Swal.fire('⚠️ Error', data.message, 'error');
              }
            })
            .catch(err => {
              console.error(err);
              Swal.fire('⚠️ Error', 'Fallo de red o servidor.', 'error');
            });
        }
      });
    }

    /* Helpers */
    function scrollTo(id) {
      const el = document.getElementById(id);
      if (el) el.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
      });
    }

    function cerrarSesion() {
      Swal.fire({
        title: "¿Cerrar sesión?",
        text: "Tu sesión se cerrará.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, salir",
        confirmButtonColor: "#6a1b1b"
      }).then(r => {
        if (r.isConfirmed) location.href = 'logout.php'
      });
    }

    /* Inicial */
    renderCart();
  </script>
  <?php if (!$estado): ?>
    <!-- 🚫 Overlay de bloqueo total -->
    <div id="overlay-cerrado">
      <i class="fa-solid fa-circle-xmark"></i>
      <h1>Restaurante No Disponible</h1>
      <p>🚫 En este momento no estamos atendiendo pedidos.<br>Por favor, vuelve más tarde.</p>
      <a href="index.php" class="btn-volver">Volver al inicio</a>
      <small>Cloud Food — Gracias por tu comprensión</small>
    </div>
    <script>
      // 🔒 Desactivar cualquier interacción
      document.addEventListener('DOMContentLoaded', () => {
        document.body.style.overflow = 'hidden';
        document.querySelectorAll('button, a, input, select').forEach(el => el.disabled = true);
      });
    </script>
  <?php endif; ?>
</body>

</html>