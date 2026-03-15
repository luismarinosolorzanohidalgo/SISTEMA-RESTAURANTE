<?php
include 'conexion.php';
session_start();

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
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Login Trabajador - Cloud Food</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>

<style>

:root{
--primary:#0f4c81;
--secondary:#1e3a8a;
--accent:#22d3ee;
--bg-dark:#0b0c10;
--bg-light:rgba(255,255,255,0.05);
}

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Segoe UI','Segoe UI Emoji','Noto Color Emoji',sans-serif;
}

body{

height:100vh;
display:flex;
align-items:center;
justify-content:center;

background:linear-gradient(135deg,var(--secondary),var(--primary));

color:white;
overflow:hidden;

}

#particles{
position:absolute;
width:100%;
height:100%;
top:0;
left:0;
z-index:0;
}

.login-box{

position:relative;
z-index:2;

width:380px;
padding:40px;

background:var(--bg-light);

backdrop-filter:blur(12px);

border-radius:18px;

box-shadow:0 10px 35px rgba(0,0,0,0.5);

animation:fadeUp .8s ease;

}

@keyframes fadeUp{
from{opacity:0; transform:translateY(60px);}
to{opacity:1; transform:translateY(0);}
}

.logo{
text-align:center;
margin-bottom:25px;
}

.logo img{
width:170px;
}

.input-group{
position:relative;
margin-bottom:20px;
}

.input-group i{
position:absolute;
left:12px;
top:14px;
color:var(--accent);
}

.input-group input,
.input-group select{

width:100%;

padding:12px 12px 12px 38px;

border-radius:10px;

border:none;

background:rgba(255,255,255,0.1);

color:white;

outline:none;

transition:.3s;

}

.input-group input:focus,
.input-group select:focus{

box-shadow:0 0 10px var(--accent);

background:rgba(255,255,255,0.15);

}

select option{
color:black;
}

.btn{

width:100%;

padding:13px;

border:none;

border-radius:40px;

font-size:15px;

font-weight:bold;

background:var(--accent);

color:#000;

cursor:pointer;

transition:.3s;

}

.btn:hover{

transform:scale(1.05);

background:linear-gradient(90deg,var(--primary),var(--secondary));

color:white;

box-shadow:0 10px 25px rgba(0,0,0,0.4);

}

.links{

margin-top:20px;

display:flex;

justify-content:space-between;

font-size:13px;

}

.links a{

color:white;

text-decoration:none;

opacity:.8;

transition:.3s;

}

.links a:hover{

opacity:1;
color:var(--accent);

}

</style>
</head>

<body>

<canvas id="particles"></canvas>

<div class="login-box">

<div class="logo">
<img src="uploads/Logo_cloud_food_en_oscuro-transparente (1).png">
</div>

<form id="loginForm">

<div class="input-group">
<i class="fa fa-envelope"></i>
<input type="email" name="correo" required placeholder="Correo electrónico">
</div>

<div class="input-group">
<i class="fa fa-store"></i>
<select name="sede" required>

<option value="">Seleccione sede</option>

<?php foreach ($sedes as $sede): ?>

<option value="<?= htmlspecialchars($sede['id']) ?>">
<?= htmlspecialchars($sede['nombre']) ?>
</option>

<?php endforeach; ?>

</select>
</div>

<div class="input-group">
<i class="fa fa-lock"></i>
<input type="password" name="password" required placeholder="Contraseña">
</div>

<button class="btn">
<i class="fa fa-right-to-bracket"></i>
 Iniciar Sesión
</button>

</form>

<div class="links">
<a href="registro_trabajador.php">Registrarse</a>
<a href="index.php">Volver</a>
</div>

</div>

<script>

let intentos = 0;
let bloqueado = false;

document.getElementById("loginForm").addEventListener("submit",async function(e){

e.preventDefault();

if(bloqueado) return;

const data=new FormData(this);

const res=await fetch("procesar_login_trabajador.php",{method:"POST",body:data});
const result=await res.json();

if(result.success){

const nombre=result.nombre || "trabajador";

const speech=new SpeechSynthesisUtterance(`Bienvenido ${nombre} a Cloud Food`);
speech.lang="es-ES";
speechSynthesis.speak(speech);

Swal.fire({
title:"Bienvenido",
text:result.message,
icon:"success",
timer:2000,
showConfirmButton:false
});

const end=Date.now()+1500;

(function frame(){
confetti({particleCount:5,angle:60,spread:70,origin:{x:0}});
confetti({particleCount:5,angle:120,spread:70,origin:{x:1}});
if(Date.now()<end)requestAnimationFrame(frame);
})();

setTimeout(()=>{
window.location="index_trabajador.php";
},2000);

}else{

intentos++;

if(intentos>=3){

bloqueado=true;

let segundos=60;

const timer=setInterval(()=>{

Swal.fire({
title:"Bloqueado",
html:`Demasiados intentos<br>Espera <b>${segundos}</b> segundos`,
icon:"error",
showConfirmButton:false,
allowOutsideClick:false
});

segundos--;

if(segundos<0){

clearInterval(timer);
bloqueado=false;
intentos=0;
Swal.close();

}

},1000);

}else{

Swal.fire({
icon:"error",
title:"Error",
text:result.message
});

}

}

});

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
vx:(Math.random()-0.5)*0.3,
vy:(Math.random()-0.5)*0.3
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
ctx.fillStyle="rgba(34,211,238,0.7)";
ctx.fill();

});

requestAnimationFrame(animate);

}

animate();

</script>

</body>
</html>