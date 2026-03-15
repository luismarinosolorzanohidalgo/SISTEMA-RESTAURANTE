<?php
// logout.php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Logout - Cloud Food</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      margin: 0;
      height: 100vh;
      background: #000; /* Pantalla negra inmediata */
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
    }
    audio { display: none; }
  </style>
</head>
<body>

<!-- 🔊 Sonido tipo iPhone -->
<audio id="logoutSound" preload="auto">
  <source src="https://assets.mixkit.co/sfx/preview/mixkit-software-interface-back-2575.mp3" type="audio/mpeg">
</audio>

<script>
  document.addEventListener("DOMContentLoaded", () => {
    // Reproducir sonido inmediatamente
    const sound = document.getElementById("logoutSound");
    sound.play().catch(() => {});

    // ✅ Mostrar alerta con texto limpio
    Swal.fire({
      title: "👋 ¡Hasta pronto!",
  text: "Tu sesión en Cloud Food fue cerrada correctamente", // 👈 ya no usa html
      icon: "success",
      timer: 2000,
      showConfirmButton: false,
      allowOutsideClick: false,
      background: "rgba(0,0,0,0.9)",
      color: "#fff",
      backdrop: `
        rgba(0,0,0,0.7)
        url("https://media.giphy.com/media/f9R0yXxQbz8G1hJg5j/giphy.gif")
        center top
        no-repeat
      `
    }).then(() => {
      // ✅ Redirección automática sin necesidad de refrescar
      window.location.replace("index.php");
    });
  });
</script>

</body>
</html>
