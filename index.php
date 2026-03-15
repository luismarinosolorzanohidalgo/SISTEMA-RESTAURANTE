<?php
include 'conexion.php';
session_start();

$clienteLogeado = isset($_SESSION['cliente']);
$trabajadorLogeado = isset($_SESSION['trabajador']);
$nombreUsuario = $clienteLogeado ? $_SESSION['cliente'] : ($trabajadorLogeado ? $_SESSION['trabajador'] : null);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cloud Food Pro</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
:root{
  --primary:#0f4c81;      /* azul eléctrico */
  --secondary:#1e3a8a;    /* azul oscuro */
  --accent:#22d3ee;       /* azul brillante */
  --bg-dark:#0b0c10;
  --bg-light:rgba(255,255,255,0.05);
}

/* RESET */
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}

/* FONDO */
body{
  height:100vh;
  background:linear-gradient(135deg,var(--secondary),var(--primary));
  display:flex;
  justify-content:center;
  align-items:center;
  overflow-x:hidden;
  color:white;
}

/* TOPBAR */
.topbar{
  position:fixed;
  top:0;
  width:100%;
  height:65px;
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding:0 25px;
  background:var(--bg-light);
  backdrop-filter:blur(10px);
  z-index:100;
  box-shadow:0 4px 10px rgba(0,0,0,0.2);
}

.menu-btn{
  font-size:26px;
  cursor:pointer;
  transition:.3s;
}

.menu-btn:hover{color:var(--accent);}

/* SIDEBAR */
.sidebar{
  position:fixed;
  top:0;
  left:-280px;
  width:280px;
  height:100%;
  background:var(--bg-dark);
  color:white;
  padding:30px 20px;
  transition:.35s;
  box-shadow:5px 0 25px rgba(0,0,0,0.5);
  z-index:200;
  display:flex;
  flex-direction:column;
}

.sidebar.active{left:0;}

.sidebar .close-btn{
  font-size:22px;
  cursor:pointer;
  align-self:flex-end;
  margin-bottom:30px;
}

.sidebar h2{
  color:var(--accent);
  margin-bottom:30px;
  text-align:center;
}

.sidebar a{
  display:flex;
  align-items:center;
  gap:15px;
  padding:12px;
  margin-bottom:10px;
  border-radius:10px;
  text-decoration:none;
  color:white;
  transition:.3s;
  font-weight:500;
}

.sidebar a:hover{
  background:linear-gradient(90deg,var(--primary),var(--secondary));
  transform:translateX(5px);
}

/* CONTENIDO CENTRAL */
.main{
  display:flex;
  justify-content:center;
  align-items:center;
  text-align:center;
  width:100%;
  min-height:100vh;
  padding:20px;
}

/* HERO */
.hero h1{
  font-size:80px;
  font-weight:900;
  text-transform:uppercase;
  background:linear-gradient(90deg,var(--accent),var(--primary));
  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
  text-shadow:0 0 25px rgba(0,0,0,0.5);
  margin-bottom:20px;
  animation:fadeInDown 1s ease forwards;
}

.hero p{
  font-size:26px;
  margin-bottom:40px;
  animation:fadeIn 1.5s ease forwards;
  opacity:0.95;
}

/* CTA */
.hero a{
  padding:18px 36px;
  border-radius:50px;
  font-size:20px;
  font-weight:bold;
  background:var(--accent);
  color:white;
  text-decoration:none;
  transition:all 0.4s ease;
  box-shadow:0 8px 25px rgba(0,0,0,0.3);
}

.hero a:hover{
  background:linear-gradient(90deg,var(--primary),var(--secondary));
  transform:scale(1.1);
  box-shadow:0 10px 35px rgba(0,0,0,0.5);
}

/* ANIMACIONES */
@keyframes fadeInDown{
  0%{opacity:0; transform:translateY(-50px);}
  100%{opacity:1; transform:translateY(0);}
}

@keyframes fadeIn{
  0%{opacity:0;}
  100%{opacity:1;}
}

/* TEXTO DINÁMICO */
.dynamic-text span{
  display:none;
  font-weight:bold;
  text-shadow:0 0 10px var(--secondary),0 0 20px var(--accent);
}

.dynamic-text span.active{display:inline;}

</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
  <div class="close-btn" onclick="toggleMenu()"><i class="fa fa-times"></i></div>
  <h2>Cloud Food</h2>
  <a href="#"><i class="fa fa-home"></i> Inicio</a>
  <a href="menu.php"><i class="fa fa-utensils"></i> Menú</a>
  <a href="#"><i class="fa fa-motorcycle"></i> Pedidos</a>
  <a href="login_cliente.php"><i class="fa fa-users"></i> Clientes</a>
  <?php if($trabajadorLogeado): ?>
  <a href="panel_trabajador.php"><i class="fa fa-gear"></i> Gestión</a>
  <a href="graficos.php"><i class="fa fa-chart-line"></i> Gráficos</a>
  <?php endif; ?>
  <a href="logout.php"><i class="fa fa-right-from-bracket"></i> Salir</a>
</div>

<!-- TOPBAR -->
<div class="topbar">
  <div class="menu-btn" onclick="toggleMenu()"><i class="fa fa-bars"></i></div>
  <div><?php if($nombreUsuario): ?>👋 <?= htmlspecialchars($nombreUsuario) ?><?php endif; ?></div>
</div>

<!-- HERO -->
<div class="main">
  <div class="hero">
    <h1>Cloud Food</h1>
    <p class="dynamic-text">
      <span class="active">¡Delicioso y rápido!</span>
      <span>Gestiona tu menú con estilo.</span>
      <span>Entrega garantizada 🚀🍔</span>
    </p>
    <a href="menu.php"><i class="fa fa-burger"></i> Ver Menú</a>
  </div>
</div>

<script>
function toggleMenu(){
  document.getElementById("sidebar").classList.toggle("active");
}

// Texto dinámico tipo typing glow
const phrases = document.querySelectorAll('.dynamic-text span');
let i = 0;
setInterval(()=>{
  phrases.forEach(p=>p.classList.remove('active'));
  phrases[i].classList.add('active');
  i = (i+1) % phrases.length;
},4000);
</script>

</body>
</html>