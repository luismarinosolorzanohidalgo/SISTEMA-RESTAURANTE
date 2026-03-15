<?php
// panel_trabajador.php
include 'conexion.php';
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <title>Panel Trabajador • Cloud Food</title>

  <!-- Bootstrap + FontAwesome + SweetAlert -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="assets/site-logo.css">

  <style>
    /* ---------- variables ---------- */
    :root {
      --bg: #f4f7fb;
      --card: #ffffff;
      --accent-1: #ff6a00;
      --accent-2: #e65600;
      --muted: #6b7280;
      --glass: rgba(255, 255, 255, 0.85);
      --shadow: 0 10px 30px rgba(30, 30, 50, 0.06);
      --radius: 14px;
    }

    /* ---------- base ---------- */
    * {
      box-sizing: border-box
    }

    body {
      margin: 0;
      background: linear-gradient(180deg, #f7fbff, #eef6fb);
      font-family: Inter, "Segoe UI", Roboto, Arial, sans-serif;
      color: #111827;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      min-height: 100vh;
    }

    .topbar {
      background: linear-gradient(90deg, var(--accent-1), var(--accent-2));
      color: white;
      padding: 16px 28px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
      position: sticky;
      top: 0;
      z-index: 50;
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .brand .logo {
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    }

    /* Hide verbose labels next to the logo to keep topbar compact */
    .brand .brand-text { display: none; }

    /* ---------- layout ---------- */
    .layout {
      max-width: 1200px;
      margin: 28px auto;
      padding: 0 18px;
      display: grid;
      grid-template-columns: 320px 1fr;
      gap: 20px;
    }

    .sidebar {
      background: var(--glass);
      border-radius: var(--radius);
      padding: 18px;
      box-shadow: var(--shadow);
      position: sticky;
      top: 24px;
      height: fit-content;
    }

    .panel {
      padding: 6px 0;
    }

    .controls {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 12px
    }

    .controls .left h3 {
      margin: 0
    }

    .controls .right {
      display: flex;
      gap: 10px;
      align-items: center
    }

    /* ---------- cards / pedidos ---------- */
    #listaPedidos {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 18px;
    }

    .pedido-card {
      background: #ffffff;
      border-radius: 14px;
      padding: 16px 18px;
      margin-bottom: 16px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
      border: 1px solid #eceff1;
      transition: all 0.25s ease;
    }

    .pedido-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    }

    .pedido-top {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px dashed #e0e0e0;
      padding-bottom: 8px;
      margin-bottom: 10px;
    }

    .pedido-top .meta {
      font-weight: 600;
      color: #374151;
    }

    .pedido-top .precio {
      font-size: 1rem;
      font-weight: bold;
      color: #111827;
    }

    .pedido-body {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .pedido-lista {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .pedido-lista li {
      display: flex;
      justify-content: space-between;
      padding: 6px 0;
      border-bottom: 1px solid #f0f0f0;
      font-size: 0.93rem;
    }

    .pedido-lista .nombre {
      font-weight: 500;
      color: #374151;
    }

    .pedido-lista .cantidad {
      color: #6b7280;
      font-weight: 600;
    }

    .pedido-meta {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 10px;
      flex-wrap: wrap;
      gap: 10px;
    }

    .pedido-meta small {
      color: #9ca3af;
    }

    .acciones {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      align-items: center;
    }

    .tiempo-select {
      padding: 4px 8px;
      border-radius: 6px;
      border: 1px solid #d1d5db;
      background: #f9fafb;
      font-size: 0.85rem;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .tiempo-select:hover {
      background: #eef2ff;
    }

    .badge-estado {
      padding: 4px 8px;
      border-radius: 6px;
      font-size: 0.8rem;
      font-weight: 600;
    }

    .badge-estado.pendiente {
      background: #fff7ed;
      color: #d97706;
    }

    .badge-estado.en-preparación {
      background: #eff6ff;
      color: #2563eb;
    }

    .badge-estado.en-camino {
      background: #ecfdf5;
      color: #059669;
    }

    .badge-estado.entregado {
      background: #f0fdf4;
      color: #16a34a;
    }

    .steps {
      display: flex;
      justify-content: space-between;
      margin-top: 8px;
    }

    .step {
      text-align: center;
      flex: 1;
      font-size: 0.7rem;
      color: #9ca3af;
    }

    .dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      margin: 4px auto;
      background: #d1d5db;
    }

    .step.active .dot {
      background: #3b82f6;
    }

    .step.complete .dot {
      background: #22c55e;
    }

    .pedido-top .meta small {
      display: block;
      font-weight: 600;
      color: var(--muted);
      font-size: 12px
    }

    .badge-estado {
      padding: 6px 10px;
      border-radius: 999px;
      font-weight: 700;
      font-size: 0.8rem;
      color: #fff;
      display: inline-block;
    }

    /* estados */
    .Pendiente {
      background: #f59e0b
    }

    .En_preparacion {
      background: #0ea5e9
    }

    .En_camino {
      background: #7c3aed
    }

    .Entregado {
      background: #10b981
    }

    .Cancelado {
      background: #ef4444
    }

    .Rechazado {
      background: #ef4444
    }

    .pedido-body {
      display: flex;
      flex-direction: column;
      gap: 8px
    }

    .pedido-items {
      font-size: 0.95rem;
      color: #374151
    }

    .pedido-meta {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 8px
    }

    /* timeline */
    .steps {
      display: flex;
      gap: 8px;
      align-items: center;
      margin-top: 8px
    }

    .step {
      flex: 1;
      text-align: center;
      font-size: 12px;
      color: var(--muted)
    }

    .dot {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      background: #eef2f7;
      margin: 0 auto 6px auto;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.04)
    }

    .step.active .dot {
      background: linear-gradient(90deg, var(--accent-1), var(--accent-2))
    }

    .step.complete .dot {
      background: #10b981
    }

    /* action buttons */
    .actions {
      display: flex;
      gap: 8px;
      flex-wrap: wrap
    }

    .actions .btn {
      font-weight: 700;
      border-radius: 10px;
      padding: 8px 10px
    }

    /* floating new order panel */
    .new-order-popup {
      position: fixed;
      left: 50%;
      transform: translateX(-50%);
      bottom: 26px;
      z-index: 120;
      background: linear-gradient(90deg, #fff, #fff);
      border-radius: 12px;
      padding: 14px 18px;
      box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
      display: flex;
      gap: 12px;
      align-items: center;
      min-width: 360px;
      border-left: 6px solid var(--accent-1);
      transition: all .25s ease;
    }

    .new-order-popup.hide {
      opacity: 0;
      pointer-events: none;
      transform: translateX(-50%) translateY(20px)
    }

    .new-order-popup .info {
      flex: 1
    }

    .new-order-popup .actions {
      display: flex;
      gap: 8px
    }

    /* volver flotante */
    .volver-btn {
      position: fixed;
      right: 24px;
      bottom: 24px;
      background: linear-gradient(135deg, var(--accent-1), var(--accent-2));
      color: white;
      border: none;
      padding: 12px 18px;
      border-radius: 12px;
      font-weight: 700;
      z-index: 110;
      box-shadow: 0 12px 30px rgba(230, 90, 30, 0.18)
    }

    /* responsive */
    @media (max-width:980px) {
      .layout {
        grid-template-columns: 1fr
      }

      .new-order-popup {
        left: 12px;
        right: 12px;
        transform: none;
        min-width: auto
      }
    }
  </style>
</head>

<body>
  <div class="topbar">
    <div class="brand">
      <div class="logo"><img src="uploads/Logo_cloud_food_en_oscuro-transparente (1).png" alt="Cloud Food" class="site-logo site-logo--nav" loading="lazy" decoding="async"></div>
      <!-- brand-text removed to keep the topbar compact -->
    </div>
    <div>
      <button class="btn btn-light me-2" onclick="location.href='platos_crud.php'"><i class="fa-solid fa-utensils"></i> Gestionar platos</button>
      <button class="btn btn-outline-light" onclick="location.href='logout.php'"><i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión</button>
    </div>
  </div>

  <audio id="notifSound" preload="auto">
    <source src="../restaurante/sonido/nuevo_pedido.mp3" type="audio/mpeg" />
    Tu navegador no soporta audio HTML5.
  </audio>



  <div class="layout">
    <!-- SIDEBAR -->
    <aside class="sidebar">
      <h5 style="margin:0 0 6px 0">Resumen rápido</h5>
      <div style="color:var(--muted);font-size:14px;margin-bottom:12px">Pedidos en tiempo real • Acepta los entrantes para gestionarlos</div>
      <hr />
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
        <small class="muted">Última actualización</small><small id="lastUpdate" class="muted">—</small>
      </div>

      <div style="margin-top:12px">
        <small class="muted">Leyenda</small>
        <div class="mt-2 d-flex flex-column gap-2">
          <span><span class="badge-estado Pendiente">Pendiente</span> Nuevo pedido</span>
          <span><span class="badge-estado En_preparacion">En preparación</span> Cocina</span>
          <span><span class="badge-estado En_camino">En camino</span> Entrega</span>
          <span><span class="badge-estado Entregado">Entregado</span> Finalizado</span>
          <span><span class="badge-estado Cancelado">Cancelado</span> Anulado</span>
        </div>
      </div>
      <hr />
      <div>
        <small class="muted">Acciones rápidas</small>
        <div class="mt-2 d-flex gap-2">
          <button class="btn btn-sm btn-outline-secondary" id="forceRefresh"><i class="fa-solid fa-arrows-rotate"></i> Refrescar</button>
          <button class="btn btn-sm btn-outline-info" id="clearNotifs">Limpiar notifs</button>
        </div>
      </div>
    </aside>

    <!-- MAIN -->
    <main class="panel">
      <div class="controls">
        <div class="left">
          <h3 style="margin:0">Pedidos activos</h3>
          <small class="muted">Acepta o rechaza los pedidos entrantes. Una vez aceptados, gestiona su avance.</small>
        </div>
        <div class="right">
          <div class="me-2"><small id="stats" class="muted">—</small></div>
        </div>
      </div>

      <div id="listaPedidos" aria-live="polite"></div>
    </main>
  </div>

  <!-- floating new order -->
  <div id="newOrderPopup" class="new-order-popup hide" role="dialog" aria-live="assertive" aria-atomic="true">
    <div style="display:flex;gap:12px;align-items:center">
      <div style="width:56px;height:56px;border-radius:10px;background:linear-gradient(90deg,var(--accent-1),var(--accent-2));display:flex;align-items:center;justify-content:center;color:white;font-weight:800;font-size:18px">
        📦
      </div>
    </div>
    <div class="info">
      <div id="newOrderTitle" style="font-weight:800">Nuevo pedido</div>
      <div id="newOrderSub" style="color:var(--muted);font-size:13px">—</div>
    </div>
    <div class="actions">
      <button id="btnAcceptPopup" class="btn btn-sm btn-success">Aceptar</button>
      <button id="btnRejectPopup" class="btn btn-sm btn-danger">Rechazar</button>
    </div>
  </div>

  <button id="btnVolver" class="volver-btn"><i class="fa-solid fa-arrow-left"></i> Volver al inicio</button>

  <!-- sonidos -->
  <audio id="notifSound" preload="auto">
    <source src="https://assets.mixkit.co/sfx/preview/mixkit-positive-notification-951.mp3" type="audio/mpeg">
  </audio>

  <script>
    // ------------------------
    // Fix: desbloqueo de audio
    // ------------------------
    document.body.addEventListener('click', () => {
      // al primer click, habilitamos el canal
      const audio = document.getElementById('notifSound');
      audio.play().then(() => {
        audio.pause(); // pausamos de inmediato, ya está desbloqueado
        audio.currentTime = 0;
      }).catch(() => {});
    }, {
      once: true
    });

    // ------------------------
    // TTS helper
    // ------------------------
    function hablar(texto) {
      try {
        const u = new SpeechSynthesisUtterance(texto);
        u.lang = 'es-ES';
        u.rate = 1;
        speechSynthesis.speak(u);
      } catch (e) {
        console.error("TTS error", e);
      }
    }

    document.body.addEventListener('click', () => {
      const audio = document.getElementById('notifSound');
      audio.play().then(() => {
        audio.pause();
        audio.currentTime = 0;
      }).catch(() => {});
    }, {
      once: true
    });

    // ------------------------
    // Cuando detectamos nuevo pedido
    // ------------------------
    async function notificarNuevoPedido(p) {
      const audio = document.getElementById('notifSound');
      try {
        await audio.play();
      } catch (e) {}

      hablar("¡NUEVO PEDIDO, ATIENDE RAPIDO!");

      // popup
      newTitle.innerText = `Pedido #${p.id} • ${p.cliente}`;
      newSub.innerHTML = `Total: <strong>S/ ${parseFloat(p.total).toFixed(2)}</strong> • ${new Date(p.fecha).toLocaleString()}`;
      popup.classList.remove('hide');

      btnAcceptPopup.onclick = async () => {
        popup.classList.add('hide');
        await aceptarPedido(p.id);
      };
      btnRejectPopup.onclick = async () => {
        popup.classList.add('hide');
        await rechazarPedido(p.id);
      };
    }

    /* -------------------------
       Frontend logic avanzado
       ------------------------- */

    const popup = document.getElementById('newOrderPopup');
    const newTitle = document.getElementById('newOrderTitle');
    const newSub = document.getElementById('newOrderSub');
    const btnAcceptPopup = document.getElementById('btnAcceptPopup');
    const btnRejectPopup = document.getElementById('btnRejectPopup');
    const notifSound = document.getElementById('notifSound');

    let ultimoId = parseInt(sessionStorage.getItem('ff_last_id') || '0', 10);
    let notified = JSON.parse(sessionStorage.getItem('ff_notified') || '[]');
    let pedidosCache = []; // cache current list

    // helpers
    const slug = s => s ? s.normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/\s+/g, '_').replace(/[^\w\-]+/g, '') : '';
    const nowText = () => new Date().toLocaleTimeString();

    // mostrar popup nuevo pedido (ya existe, pero se asegura de que esté aquí)
    function showNewOrderPopup(p) {
      newTitle.innerText = `Pedido #${p.id} • ${p.cliente}`;
      newSub.innerHTML = `Total: <strong>S/ ${parseFloat(p.total).toFixed(2)}</strong> • ${new Date(p.fecha).toLocaleString()}`;
      popup.classList.remove('hide');
      // bind actions
      btnAcceptPopup.onclick = async () => {
        popup.classList.add('hide');
        await aceptarPedido(p.id);
      };
      btnRejectPopup.onclick = async () => {
        popup.classList.add('hide');
        await rechazarPedido(p.id);
      };
    }

    // aceptar (marcar como Pendiente/aceptado)
    async function aceptarPedido(id) {
      await cambiarEstado(id, 'Pendiente', {
        silent: true
      });
      await cargarPedidos();
      Swal.fire({
        icon: 'success',
        title: 'Pedido aceptado',
        text: `Pedido #${id} agregado al panel`
      });
    }

    // rechazar
    async function rechazarPedido(id) {
      await cambiarEstado(id, 'Rechazado', {
        silent: true
      });
      await cargarPedidos();
      Swal.fire({
        icon: 'error',
        title: 'Pedido rechazado',
        text: `Pedido #${id} eliminado del flujo`
      });
    }

    // render pedidos
    function renderPedidos(data) {
      const cont = document.getElementById('listaPedidos');
      cont.innerHTML = '';
      pedidosCache = data;

      // Estadísticas
      const total = data.length;
      const pendientes = data.filter(x => x.estado === 'Pendiente').length;
      const enprep = data.filter(x => x.estado === 'En preparación').length;
      const entregados = data.filter(x => x.estado === 'Entregado').length;
      document.getElementById('stats').innerText = `Total: ${total} • Pendientes: ${pendientes} • En prep: ${enprep} • Entregados: ${entregados}`;

      if (total === 0) {
        cont.innerHTML = `<div class="alert alert-info">No hay pedidos activos.</div>`;
        return;
      }

      data.forEach(p => {
        if (p.estado === 'Rechazado') return;

        // Pasos del pedido (timeline)
        const steps = ['Pendiente', 'En preparación', 'En camino', 'Entregado'];
        const currentIdx = steps.indexOf(p.estado);
        let stepsHtml = '<div class="steps">';
        steps.forEach((s, i) => {
          const cls = i < currentIdx ? 'complete' : (i === currentIdx ? 'active' : '');
          stepsHtml += `<div class="step ${cls}"><div class="dot"></div><div>${s}</div></div>`;
        });
        stepsHtml += '</div>';

        // Acciones según estado
        let accionesHtml = '';
        if (p.estado === 'Pendiente') {
          accionesHtml = `
        <button class="btn btn-sm btn-outline-warning" onclick="confirmCambio(${p.id}, 'En preparación')">
          <i class="fa-solid fa-bowl-food"></i> Preparar
        </button>
        <button class="btn btn-sm btn-outline-danger" onclick="confirmCambio(${p.id}, 'Cancelado')">
          <i class="fa-solid fa-xmark"></i> Cancelar
        </button>`;
        } else if (p.estado === 'En preparación') {
          accionesHtml = `
        <button class="btn btn-sm btn-outline-primary" onclick="confirmCambio(${p.id}, 'En camino')">
          <i class="fa-solid fa-truck"></i> En camino
        </button>
        <button class="btn btn-sm btn-outline-danger" onclick="confirmCambio(${p.id}, 'Cancelado')">
          <i class="fa-solid fa-xmark"></i> Cancelar
        </button>`;
        } else if (p.estado === 'En camino') {
          accionesHtml = `
        <button class="btn btn-sm btn-outline-success" onclick="confirmCambio(${p.id}, 'Entregado')">
          <i class="fa-solid fa-check"></i> Entregado
        </button>`;
        } else {
          accionesHtml = `<button class="btn btn-sm btn-secondary" disabled>${p.estado}</button>`;
        }

        // Platos (múltiples ítems)
        let itemsHtml = '';
        if (p.items && Array.isArray(p.items) && p.items.length > 0) {
          itemsHtml = `
        <ul class="pedido-lista">
          ${p.items.map(item => `
            <li>
              <span class="nombre">${item.nombre}</span>
              <span class="cantidad">x${item.cantidad}</span>
            </li>
          `).join('')}
        </ul>`;
        } else {
          itemsHtml = '<i>Sin detalles de platos</i>';
        }

        // Select tiempo estimado
        const tiempoSelect = `
      <select class="tiempo-select" onchange="setTiempoEstimado(${p.id}, this.value)">
        <option value="">Tiempo estimado</option>
        <option>10 min</option>
        <option>15 min</option>
        <option>20 min</option>
        <option>30 min</option>
        <option>45 min</option>
      </select>`;

        // Crear tarjeta
        const card = document.createElement('div');
        card.className = 'pedido-card';
        card.innerHTML = `
      <div class="pedido-top">
        <div>
          <div class="meta">Pedido #${p.id}</div>
          <small class="muted">${p.cliente}</small>
        </div>
        <div style="text-align:right">
          <div class="precio">S/ ${parseFloat(p.total).toFixed(2)}</div>
          <div><span class="badge-estado ${slug(p.estado)}">${p.estado}</span></div>
        </div>
      </div>

      <div class="pedido-body">
        ${itemsHtml}
        ${stepsHtml}
        <div class="pedido-meta">
          <small class="muted">${new Date(p.fecha).toLocaleString()}</small>
          <div class="acciones">
            ${tiempoSelect}
            ${accionesHtml}
          </div>
        </div>
      </div>
    `;

        cont.appendChild(card);
      });
    }

    function setTiempoEstimado(idPedido, tiempo) {
      if (!tiempo) return;
      Swal.fire({
        icon: 'info',
        title: 'Tiempo estimado actualizado',
        text: `Pedido #${idPedido} → ${tiempo}`,
        timer: 1800,
        showConfirmButton: false
      });
    }


    // confirmar cambio con SweetAlert
    function confirmCambio(id, estado) {
      Swal.fire({
        title: `¿Cambiar a "${estado}"?`,
        text: `Actualizar pedido #${id} a "${estado}".`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Sí, actualizar'
      }).then(res => {
        if (res.isConfirmed) cambiarEstado(id, estado);
      });
    }

    // llamada para cambiar estado
   async function cambiarEstado(id, estado, opts = {}) {
  try {
    const form = new URLSearchParams();
    form.append('id', String(id));
    form.append('estado', estado);

    const r = await fetch('update_estado.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: form.toString(),
      credentials: 'same-origin'     // incluye cookies/sesión si las usas
    });

    if (!r.ok) throw new Error(`HTTP ${r.status}`);

    const j = await r.json().catch(() => { throw new Error('Respuesta no es JSON'); });

    if (j.success || j.status === 'success') {
      if (!opts.silent) {
        Swal.fire('✅ Actualizado', j.message || 'Estado cambiado', 'success');
      }
      await cargarPedidos();
      return j;
    } else {
      Swal.fire('❌ Error', j.message || 'No se pudo actualizar', 'error');
      return j;
    }
  } catch (e) {
    console.error('cambiarEstado error', e);
    if (!opts.silent) Swal.fire('❌ Error', e.message || 'Error en la solicitud', 'error');
    throw e;
  }
}


    // cargar pedidos via AJAX
    let firstLoad = true;
    async function cargarPedidos() {
      try {
        const res = await fetch('pedidos_ajax.php');
        const data = await res.json();

        // guard check
        if (!Array.isArray(data)) {
          console.error('pedidos_ajax no devolvió array', data);
          return;
        }

        // sort by id desc
        data.sort((a, b) => b.id - a.id);

        // detectar nuevos
        if (firstLoad) {
          // initialize ultimoId to highest id present (no notification on first load)
          ultimoId = data.length ? Math.max(...data.map(x => x.id)) : 0;
          firstLoad = false;
        } else {
          const maxId = data.length ? Math.max(...data.map(x => x.id)) : 0;
          if (maxId > ultimoId) {
            // buscar los nuevos específicamente
            const nuevos = data.filter(x => x.id > ultimoId);
            for (const p of nuevos.reverse()) {
              // notify once per id
              if (!notified.includes(p.id)) {
                // play sound & tts
                notifSound.play().catch(() => {});
                const u = new SpeechSynthesisUtterance('¡NUEVO PEDIDO, ATIENDE RAPIDOO!');
                u.lang = 'es-ES';
                u.rate = 1;
                speechSynthesis.speak(u);

                // show popup with accept/reject
                showNewOrderPopup(p);
                notified.push(p.id);
                sessionStorage.setItem('ff_notified', JSON.stringify(notified));
                // break to show one-by-one
                break;
              }
            }
            ultimoId = maxId;
            sessionStorage.setItem('ff_last_id', String(ultimoId));
          }
        }

        // compute items preview (assumes pedidos_ajax returns items string or details)
        const mapped = data.map(p => {
          // if backend returns items JSON, you can build preview here.
          if (p.items && typeof p.items === 'string') {
            // if stringified, keep short preview
            p.items_preview = p.items.length > 120 ? p.items.slice(0, 120) + '...' : p.items;
          } else if (Array.isArray(p.items)) {
            p.items_preview = p.items.map(i => `${i.cantidad}x ${i.nombre}`).slice(0, 4).join(', ');
          } else {
            p.items_preview = '';
          }
          return p;
        });

        renderPedidos(mapped);
        document.getElementById('lastUpdate').innerText = nowText();
      } catch (e) {
        console.error('error cargarPedidos', e);
      }
    }

    // misc actions
    document.getElementById('forceRefresh').addEventListener('click', cargarPedidos);
    document.getElementById('clearNotifs').addEventListener('click', () => {
      notified = [];
      sessionStorage.setItem('ff_notified', JSON.stringify([]));
      Swal.fire('Hecho', 'Notificaciones limpiadas', 'success');
    });

    // volver
    document.getElementById('btnVolver').addEventListener('click', () => {
      notifSound.play().catch(() => {});
      Swal.fire({
          title: '🔄 Volviendo...',
          text: 'Redirigiendo al inicio',
          icon: 'info',
          timer: 1300,
          showConfirmButton: false
        })
        .then(() => window.location.href = 'index_trabajador.php');
    });

    // inicializar
    cargarPedidos();
    setInterval(cargarPedidos, 10000);
  </script>

</body>

</html>