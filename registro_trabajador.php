<?php
include 'conexion.php';
session_start();

// Obtenemos las sedes desde la BD
$sedes = [];
$sql = "SELECT id, nombre FROM sedes ORDER BY nombre ASC";
$result = $conexion->query($sql);

if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $sedes[] = $row;
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro Trabajador - Cloud Food</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="assets/site-logo.css">
  <style>
    :root {
      --primary: #ffe680; /* dorado más claro */
      --secondary: #6a1b1b; /* vino oscuro */
      --text-light: #fff;
    }

    body {
      font-family: 'Segoe UI', Tahoma, sans-serif;
      margin: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: linear-gradient(135deg, #1c1c1c, #2b2b2b);
      overflow: hidden;
      color: var(--text-light);
    }

    /* Canvas para partículas */
    #particles {
      position: absolute;
      top: 0; left: 0;
      width: 100%; height: 100%;
      z-index: 0;
      pointer-events: none;
    }

    /* Caja registro */
    .register-box {
      background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.03));
      backdrop-filter: blur(20px) saturate(160%);
      padding: 50px 40px;
      border-radius: 22px;
      box-shadow: 0 15px 45px rgba(0,0,0,0.8);
      width: 420px;
      text-align: center;
      position: relative;
      z-index: 1;
      animation: floatIn 1.2s ease-out, glow 6s infinite alternate;
      border: 1px solid rgba(255,255,255,0.15);
    }
    @keyframes floatIn {
      from { transform: translateY(80px) scale(0.9); opacity: 0; }
      to { transform: translateY(0) scale(1); opacity: 1; }
    }
    @keyframes glow {
      from { box-shadow: 0 0 15px rgba(255,230,128,0.4); }
      to { box-shadow: 0 0 35px rgba(255,230,128,0.7); }
    }

    .register-box h2 {
      font-size: 2.3rem;
      margin-bottom: 30px;
      background: linear-gradient(to right, var(--primary), var(--secondary));
      background-clip: text;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      text-shadow: 0 2px 12px rgba(0,0,0,0.6);
    }

    /* Input y Select */
    .input-group {
      position: relative;
      margin-bottom: 28px;
      text-align: left;
    }
    .input-group i {
      position: absolute;
      top: 14px;
      left: 12px;
      color: var(--primary);
      font-size: 1.1rem;
    }
    .input-group input,
    .input-group select {
      display: block;
      width: calc(100% - 40px);
      padding: 14px 12px 14px 40px;
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: 12px;
      background: rgba(255,255,255,0.08);
      color: var(--text-light);
      font-size: 1rem;
      outline: none;
      transition: all 0.3s;
      box-sizing: border-box;
    }
    .input-group input:focus,
    .input-group select:focus {
      background: rgba(255,255,255,0.2);
      box-shadow: 0 0 12px rgba(255,230,128,0.6);
      border-color: var(--primary);
    }
    .input-group label {
      position: absolute;
      top: -18px;
      left: 40px;
      font-size: 0.8rem;
      color: var(--primary);
    }
    .input-group select option {
      background: #1c1c1c;
      color: var(--primary);
    }

    /* Botón */
    .btn {
      width: 100%;
      padding: 15px;
      font-size: 1.05rem;
      border: none;
      border-radius: 50px;
      font-weight: bold;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: #fff;
      cursor: pointer;
      transition: 0.3s ease;
    }
    .btn:hover {
      transform: scale(1.05);
      box-shadow: 0 0 25px rgba(255,230,128,0.7);
    }

    /* Links */
    .links {
      display: flex;
      justify-content: space-between;
      margin-top: 25px;
    }
    .links a {
      color: var(--text-light);
      text-decoration: none;
      font-size: 0.9rem;
      padding: 5px 10px;
      border-radius: 8px;
      transition: 0.3s;
    }
    .links a:hover {
      background: rgba(255,255,255,0.15);
      color: var(--primary);
    }
  </style>
</head>
<body>
  <canvas id="particles"></canvas>

  <div class="register-box">
    <div style="text-align:center;margin-bottom:12px;">
      <img src="uploads/Logo_cloud_food_en_oscuro-transparente (1).png" alt="Cloud Food" class="site-logo site-logo--login" loading="lazy" decoding="async">
    </div>
    <form id="registerForm">
      <div class="input-group">
        <i class="fa-solid fa-user"></i>
        <input type="text" name="nombre" id="nombre" required placeholder="Nombre completo">
        <label for="nombre">Nombre</label>
      </div>
      <div class="input-group">
        <i class="fa-solid fa-envelope"></i>
        <input type="email" name="correo" id="correo" required placeholder="Correo electrónico">
        <label for="correo">Correo</label>
      </div>
      <div class="input-group">
        <i class="fa-solid fa-lock"></i>
        <input type="password" name="password" id="password" required placeholder="Contraseña">
        <label for="password">Contraseña</label>
      </div>
      <div class="input-group">
        <i class="fa-solid fa-store"></i>
        <select name="sede" id="sede" required>
          <option value="" disabled selected>Seleccione una sede</option>
          <?php foreach ($sedes as $sede): ?>
            <option value="<?= htmlspecialchars($sede['id']) ?>"><?= htmlspecialchars($sede['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
        <label for="sede">Sede</label>
      </div>
      <button type="submit" class="btn"><i class="fa-solid fa-paper-plane" id="submitBtn"></i> Crear Cuenta</button>
    </form>
    <div class="links">
      <a href="login_cliente.php"><i class="fa-solid fa-right-to-bracket"></i> Iniciar Sesión</a>
      <a href="index.php"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>
  </div>

  <script>
    document.getElementById("registerForm").addEventListener("submit", async function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      const submitBtn = document.getElementById("submitBtn");
      const originalText = submitBtn.textContent;

      submitBtn.disabled = true;
      submitBtn.textContent = "Registrando...";

      try {
        // CAMBIO AQUÍ: Apunta al procesador correcto de TRABAJADORES
        const response = await fetch("procesar_registro_trabajador.php", { 
            method: "POST", 
            body: formData 
        });

        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }

        const result = await response.json();
        
        if (result.success) {
            // Registro exitoso: Mostrar alerta y redirigir al login de trabajadores
            Swal.fire({
                icon: 'success',
                title: '¡Registro Exitoso!',
                text: result.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = "login_trabajador.php"; // Redirige al login de trabajadores
            });
        } else {
            // Error: Mostrar mensaje
            Swal.fire({
                icon: 'error',
                title: 'Error en el Registro',
                text: result.message
            });
        }
        } catch (error) {
        // Manejo de errores de red o fetch (esto es lo que faltaba para depurar por qué "no hace nada")
        console.error("Error en fetch:", error); // Revisa la consola del navegador para ver detalles
        Swal.fire({
            icon: 'error',
            title: 'Error de Conexión',
            text: 'No se pudo conectar al servidor. Verifica tu conexión o contacta al administrador.'
        });
    } finally {
        // Rehabilitar botón
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
      }
      });


    // Partículas doradas flotando
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
      particlesArray.forEach(p => { p.update(); p.draw(); });
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
