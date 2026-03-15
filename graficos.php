<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>📊 Dashboard Épico Mejorado</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
      color: #fff;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding-bottom: 120px;
    }

    h1 {
      text-transform: uppercase;
      font-size: 2.7rem;
      margin: 30px 0 20px;
      letter-spacing: 2px;
      text-shadow: 0 0 10px #00f5ff, 0 0 20px #00f5ff;
      animation: glow 2s infinite alternate;
    }

    @keyframes glow {
      from { text-shadow: 0 0 10px #00f5ff; }
      to { text-shadow: 0 0 25px #ff00ff, 0 0 50px #00f5ff; }
    }

    .chart-container, .table-container, .list-container {
      width: 85%;
      max-width: 950px;
      margin: 20px auto;
      background: rgba(255, 255, 255, 0.08);
      border-radius: 20px;
      padding: 30px;
      box-shadow: 0 0 40px rgba(0, 255, 255, 0.3);
      animation: floating 4s ease-in-out infinite;
    }

    @keyframes floating {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-10px); }
    }

    .header-section {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }

    select {
      background: rgba(0,245,255,0.15);
      color: #fff;
      border: 1px solid #00f5ff;
      padding: 6px 12px;
      border-radius: 10px;
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s;
    }

    select:hover {
      background: #00f5ff;
      color: #000;
    }

    canvas {
      max-height: 400px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      color: #fff;
    }

    th, td {
      padding: 12px;
      border-bottom: 1px solid #00f5ff;
      text-align: center;
    }

    th {
      background-color: rgba(0, 245, 255, 0.2);
    }

    ul {
      list-style: none;
      padding: 0;
    }

    li {
      font-size: 1.1rem;
      padding: 10px 0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }

    /* Botón flotante volver */
    .btn-volver {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background-color: #00f5ff;
      color: #000;
      border: none;
      padding: 12px 18px;
      font-weight: bold;
      border-radius: 30px;
      box-shadow: 0 0 15px #00f5ff;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-volver:hover {
      background-color: #ff00ff;
      color: #fff;
      box-shadow: 0 0 20px #ff00ff;
    }

    /* Botón flotante historial */
    .btn-historial {
      position: fixed;
      bottom: 20px;
      left: 20px;
      background-color: #ff00ff;
      color: #fff;
      border: none;
      padding: 12px 18px;
      font-weight: bold;
      border-radius: 30px;
      box-shadow: 0 0 15px #ff00ff;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-historial:hover {
      background-color: #00f5ff;
      color: #000;
      box-shadow: 0 0 20px #00f5ff;
    }

    /* Modal Historial */
    .modal {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.8);
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }

    .modal-content {
      background: #1c1c1c;
      border-radius: 20px;
      padding: 25px;
      width: 90%;
      max-width: 700px;
      color: #fff;
      box-shadow: 0 0 30px #00f5ff;
      max-height: 80vh;
      overflow-y: auto;
      animation: zoomIn 0.3s ease;
    }

    @keyframes zoomIn {
      from { transform: scale(0.8); opacity: 0; }
      to { transform: scale(1); opacity: 1; }
    }

    .close {
      float: right;
      font-size: 1.5rem;
      cursor: pointer;
      color: #ff00ff;
    }
    .close:hover { color: #00f5ff; }
  </style>
</head>
<body>
  <h1>📊 Dashboard Épico</h1>

  <!-- Pedidos -->
  <div class="chart-container">
    <div class="header-section">
      <h2>📈 Pedidos</h2>
      <select id="filtroPedidos">
        <option value="dia">Por Día</option>
        <option value="semana">Por Semana</option>
        <option value="mes">Por Mes</option>
        <option value="anio">Por Año</option>
      </select>
    </div>
    <canvas id="lineChart"></canvas>
  </div>

  <!-- Clientes -->
  <div class="table-container">
    <div class="header-section">
      <h2>📋 Clientes</h2>
      <select id="filtroClientes">
        <option value="dia">Por Día</option>
        <option value="semana">Por Semana</option>
        <option value="mes" selected>Por Mes</option>
        <option value="anio">Por Año</option>
      </select>
    </div>
    <table>
      <thead>
        <tr><th>Periodo</th><th>Total Clientes</th></tr>
      </thead>
      <tbody id="tablaClientes"></tbody>
    </table>
  </div>

  <!-- Trabajadores -->
  <div class="list-container">
    <h2>👷‍♂️ Trabajadores por Rol</h2>
    <ul id="listaTrabajadores"></ul>
  </div>

  <!-- Botones flotantes -->
  <button class="btn-volver" onclick="window.history.back()">← Volver</button>
  <button class="btn-historial" onclick="abrirHistorial()">📜 Historial</button>

  <!-- Modal Historial -->
  <div class="modal" id="modalHistorial">
    <div class="modal-content">
      <span class="close" onclick="cerrarHistorial()">&times;</span>
      <h2>📜 Historial Completo</h2>
      <div id="contenidoHistorial"></div>
    </div>
  </div>

  <script>
    let pedidosChart;

    async function cargarDatos(filtroPedidos='dia', filtroClientes='mes') {
      try {
        const res = await fetch(`datos_dashboard.php?filtroPedidos=${filtroPedidos}&filtroClientes=${filtroClientes}`);
        const data = await res.json();

        // 🎯 1. Pedidos - Chart
        if (pedidosChart) pedidosChart.destroy();
        pedidosChart = new Chart(document.getElementById('lineChart'), {
          type: 'line',
          data: {
            labels: data.pedidos.map(p => p.periodo),
            datasets: [{
              label: 'Pedidos',
              data: data.pedidos.map(p => p.total),
              borderColor: '#00f5ff',
              borderWidth: 3,
              fill: true,
              backgroundColor: 'rgba(0,245,255,0.2)',
              tension: 0.4,
              pointBackgroundColor: '#ff00ff',
              pointRadius: 6
            }]
          },
          options: {
            plugins: { legend: { labels: { color: '#fff' } } },
            scales: {
              x: { ticks: { color: '#fff' } },
              y: { ticks: { color: '#fff' } }
            }
          }
        });

        // 🎯 2. Clientes - Tabla
        const tabla = document.getElementById('tablaClientes');
        tabla.innerHTML = '';
        data.clientes.forEach(c => {
          tabla.innerHTML += `<tr><td>${c.periodo}</td><td>${c.total}</td></tr>`;
        });

        // 🎯 3. Trabajadores - Lista
        const lista = document.getElementById('listaTrabajadores');
        lista.innerHTML = '';
        data.trabajadores.forEach(t => {
          lista.innerHTML += `<li>🔹 <strong>${t.rol || 'Sin Rol'}</strong>: ${t.total}</li>`;
        });

        // 🎯 4. Historial
        const contHistorial = document.getElementById('contenidoHistorial');
        contHistorial.innerHTML = '<ul>' + data.historial.map(h => 
          `<li>📌 ${h.fecha} → Pedidos: ${h.pedidos}, Clientes: ${h.clientes}</li>`).join('') + '</ul>';

      } catch (err) {
        console.error('Error al cargar datos del dashboard:', err);
      }
    }

    // Filtros
    document.getElementById('filtroPedidos').addEventListener('change', e => {
      cargarDatos(e.target.value, document.getElementById('filtroClientes').value);
    });
    document.getElementById('filtroClientes').addEventListener('change', e => {
      cargarDatos(document.getElementById('filtroPedidos').value, e.target.value);
    });

    // Modal
    function abrirHistorial() {
      document.getElementById('modalHistorial').style.display = 'flex';
    }
    function cerrarHistorial() {
      document.getElementById('modalHistorial').style.display = 'none';
    }

    cargarDatos();
  </script>
</body>
</html>
    