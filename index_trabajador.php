<?php
session_start();

if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['Trabajador', 'Administrador', 'Cajero', 'User'])) {
  header("Location: login_trabajador.php");
  exit;
}

$nombre = $_SESSION['nombre'] ?? 'Trabajador';
?>

<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8">
<title>Panel del Trabajador - Cloud Food</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>

:root{
--primary:#2563eb;
--secondary:#1e3a8a;
--accent:#22d3ee;
--bg:#0b0c10;
--glass:rgba(255,255,255,0.06);
--border:rgba(255,255,255,0.12);
--text:#ffffff;
}

/* RESET */

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Segoe UI','Segoe UI Emoji','Noto Color Emoji','Apple Color Emoji',sans-serif;
}

body{

background:
radial-gradient(circle at 20% 20%,#1e3a8a 0%,transparent 40%),
radial-gradient(circle at 80% 80%,#2563eb 0%,transparent 40%),
#0b0c10;

color:white;
min-height:100vh;
display:flex;
flex-direction:column;
overflow-x:hidden;

}

/* HEADER */

header{

display:flex;
justify-content:space-between;
align-items:center;

padding:16px 40px;

background:rgba(0,0,0,0.35);

backdrop-filter:blur(12px);

border-bottom:1px solid var(--border);

position:sticky;
top:0;

z-index:10;

}

.logo img{
height:42px;
}

/* NAV */

nav{
display:flex;
gap:10px;
flex-wrap:wrap;
}

nav button{

background:transparent;

border:1px solid var(--accent);

color:var(--accent);

padding:9px 18px;

border-radius:30px;

cursor:pointer;

font-size:14px;

transition:all .3s ease;

}

nav button:hover{

background:linear-gradient(135deg,var(--primary),var(--accent));

color:white;

transform:translateY(-2px);

box-shadow:0 5px 18px rgba(0,0,0,0.4);

}

/* CONTENIDO */

.content{

flex:1;

display:flex;

flex-direction:column;

align-items:center;

justify-content:center;

padding:80px 20px;

text-align:center;

}

/* TITULO */

.welcome{

font-size:3rem;

font-weight:800;

background:linear-gradient(90deg,#22d3ee,#60a5fa);

-webkit-background-clip:text;

-webkit-text-fill-color:transparent;

margin-bottom:10px;

letter-spacing:1px;

}

.subtitle{

font-size:1.05rem;

opacity:.8;

margin-bottom:50px;

}

/* GRID CARDS */

.cards{

display:grid;

grid-template-columns:repeat(auto-fit,minmax(220px,1fr));

gap:30px;

width:100%;

max-width:1000px;

}

/* CARD */

.card{

background:var(--glass);

border:1px solid var(--border);

border-radius:18px;

height:150px;

display:flex;

flex-direction:column;

justify-content:center;

align-items:center;

cursor:pointer;

transition:.35s;

backdrop-filter:blur(14px);

}

.card i{

font-size:32px;

margin-bottom:10px;

color:var(--accent);

}

.card span{

font-weight:600;

font-size:16px;

}

/* HOVER CARD */

.card:hover{

transform:translateY(-8px) scale(1.05);

background:rgba(255,255,255,0.08);

box-shadow:

0 10px 30px rgba(0,0,0,0.6),
0 0 20px rgba(34,211,238,0.3);

}

/* FOOTER */

footer{

background:rgba(0,0,0,0.35);

border-top:1px solid var(--border);

text-align:center;

padding:18px;

font-size:14px;

}

footer span{
color:var(--accent);
}

/* PARTICLES */

#particles{

position:fixed;

top:0;
left:0;

width:100%;
height:100%;

z-index:-1;

}

/* RESPONSIVE */

@media(max-width:768px){

.welcome{
font-size:2.2rem;
}

header{
flex-direction:column;
gap:10px;
}

nav{
justify-content:center;
}

}

</style>
</head>

<body>

<canvas id="particles"></canvas>

<header>

<div class="logo">
<img src="uploads/Logo_cloud_food_en_oscuro-transparente (1).png">
</div>

<nav>

<button onclick="irPanelTrabajador()"><i class="fa fa-gear"></i> Gestión</button>

<button onclick="irMenu()"><i class="fa fa-book-open"></i> Menú</button>

<button onclick="irGraficos()"><i class="fa fa-chart-pie"></i> Gráficos</button>

<button onclick="toggleDisponibilidad()"><i class="fa fa-power-off"></i> Activar/Desactivar</button>

<button onclick="cerrarSesion()"><i class="fa fa-right-from-bracket"></i> Salir</button>

</nav>

</header>

<div class="content">

<h1 class="welcome">Hola, <?= htmlspecialchars($nombre) ?> 👋</h1>

<p class="subtitle">Bienvenido al panel de trabajador de <span style="color:var(--accent)">Cloud Food</span></p>

<div class="cards">

<div class="card" onclick="irPanelTrabajador()">
<i class="fa fa-gear"></i>
<span>Gestión</span>
</div>

<div class="card" onclick="irMenu()">
<i class="fa fa-book-open"></i>
<span>Ver Menú</span>
</div>

<div class="card" onclick="irGraficos()">
<i class="fa fa-chart-pie"></i>
<span>Gráficos</span>
</div>

<div class="card" onclick="cerrarSesion()">
<i class="fa fa-right-from-bracket"></i>
<span>Salir</span>
</div>

</div>

</div>

<footer>
© <?= date("Y") ?> <span>Cloud Food</span> — Panel del Trabajador
</footer>

<script>

/* FUNCIONES */

function irPanelTrabajador(){
window.location="panel_trabajador.php";
}

function irMenu(){
window.location="menu.php";
}

function irGraficos(){
window.location="graficos.php";
}

function cerrarSesion(){

Swal.fire({
title:"¿Cerrar sesión?",
icon:"warning",
showCancelButton:true,
confirmButtonText:"Sí salir"
}).then(r=>{
if(r.isConfirmed){
window.location="logout.php";
}
});

}

function toggleDisponibilidad(){

fetch("toggle_restaurante.php",{method:"POST"})
.then(r=>r.json())
.then(d=>{

Swal.fire({
title:d.mensaje,
icon:"success",
timer:1500,
showConfirmButton:false
});

});

}

/* PARTICULAS */

const canvas=document.getElementById("particles");
const ctx=canvas.getContext("2d");

canvas.width=window.innerWidth;
canvas.height=window.innerHeight;

let particles=[];

for(let i=0;i<60;i++){

particles.push({
x:Math.random()*canvas.width,
y:Math.random()*canvas.height,
size:Math.random()*2,
vx:(Math.random()-0.5)*0.4,
vy:(Math.random()-0.5)*0.4
});

}

function animate(){

ctx.clearRect(0,0,canvas.width,canvas.height);

particles.forEach(p=>{

p.x+=p.vx;
p.y+=p.vy;

if(p.x<0||p.x>canvas.width)p.vx*=-1;
if(p.y<0||p.y>canvas.height)p.vy*=-1;

ctx.beginPath();
ctx.arc(p.x,p.y,p.size,0,Math.PI*2);
ctx.fillStyle="rgba(34,211,238,0.8)";
ctx.fill();

});

requestAnimationFrame(animate);

}

animate();

</script>

</body>
</html>
