<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['cliente_id'])) {
    echo "<script>
        window.onload = function() {
            Swal.fire({
                icon: 'warning',
                title: 'Sesión expirada',
                text: 'Debes iniciar sesión para acceder al perfil.',
                confirmButtonColor: '#d4af37'
            }).then(() => { window.location = 'login.php'; });
        }
    </script>";
    exit();
}

$cliente_id = $_SESSION['cliente_id'];

// 🔹 Obtener datos del cliente
$sql = "SELECT * FROM clientes WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $cliente_id);
$stmt->execute();
$res = $stmt->get_result();
$cliente = $res->fetch_assoc();

// 🔹 Obtener sedes
$sql_sedes = "SELECT id, nombre FROM sedes";
$sedes = $conexion->query($sql_sedes);

// 🔹 Actualización
$msg = '';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);
    $sede_id = intval($_POST['sede_id']);
    $avatar = $cliente['avatar'];

    // 📷 Subir nueva imagen si existe
    if (!empty($_FILES['avatar']['name'])) {
        $directorio = "uploads/avatars/";
        if (!is_dir($directorio)) mkdir($directorio, 0777, true);
        $nombreArchivo = "avatar_" . $cliente_id . "_" . time() . "." . pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION);
        $rutaDestino = $directorio . $nombreArchivo;
        if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $rutaDestino)) $avatar = $rutaDestino;
    }

    // 🔐 Cambiar contraseña si se desea
    if (!empty($_POST['password']) && !empty($_POST['confirm_password'])) {
        if ($_POST['password'] === $_POST['confirm_password']) {
            $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $sql_update = "UPDATE clientes SET nombre=?, correo=?, telefono=?, direccion=?, sede_id=?, avatar=?, password=? WHERE id=?";
            $stmt_update = $conexion->prepare($sql_update);
            $stmt_update->bind_param("ssssissi", $nombre, $correo, $telefono, $direccion, $sede_id, $avatar, $password_hash, $cliente_id);
        } else {
            $msg = "noCoincide";
        }
    } else {
        $sql_update = "UPDATE clientes SET nombre=?, correo=?, telefono=?, direccion=?, sede_id=?, avatar=? WHERE id=?";
        $stmt_update = $conexion->prepare($sql_update);
        $stmt_update->bind_param("ssssisi", $nombre, $correo, $telefono, $direccion, $sede_id, $avatar, $cliente_id);
    }

    if (empty($msg) && isset($stmt_update) && $stmt_update->execute()) {
    $_SESSION['nombre'] = $nombre;
    // ✅ Redirección automática para refrescar los datos
    header("Location: perfil.php?updated=1");
    exit();
} else if (empty($msg)) {
    $msg = "error";
}

}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mi Perfil | Cloud Food</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

body {
  background: radial-gradient(circle at top left,#190a05 0%,#330000 40%,#0a0a0a 100%);
  color:#eee;
  font-family:'Poppins',sans-serif;
  overflow-x:hidden;
  position:relative;
}
body::before {
  content:"";
  position:fixed;
  inset:0;
  background:repeating-radial-gradient(circle at 50% 50%,rgba(255,255,255,0.05) 0,rgba(255,255,255,0.05) 2px,transparent 3px,transparent 40px);
  animation:moveBg 40s linear infinite;
  z-index:-1;
}
@keyframes moveBg {from{background-position:0 0;}to{background-position:1000px 1000px;}}

.profile-card {
  border-radius:30px;
  background:linear-gradient(145deg,#1a0f0f 0%,#111 100%);
  border:1px solid rgba(212,175,55,0.3);
  box-shadow:0 0 25px rgba(255,215,0,0.15);
  backdrop-filter:blur(10px);
  animation:float 6s ease-in-out infinite;
  position:relative;
}
@keyframes float {0%,100%{transform:translateY(0);}50%{transform:translateY(-6px);}}
.profile-card::after {
  content:"";
  position:absolute;
  inset:-3px;
  border-radius:30px;
  background:linear-gradient(45deg,rgba(255,255,255,0.2),rgba(212,175,55,0.3),rgba(255,255,255,0.1));
  filter:blur(8px);
  z-index:-1;
  animation:glow 5s ease-in-out infinite;
}
@keyframes glow {0%,100%{opacity:0.4;}50%{opacity:1;}}

.logo-container {
  width:140px;height:140px;margin:0 auto 15px;
  border-radius:50%;
  background:conic-gradient(from 0deg,#fff 0%,#d4af37 40%,#fff 80%,#d4af37 100%);
  display:flex;align-items:center;justify-content:center;
  animation:spinLight 8s linear infinite;
}
@keyframes spinLight {
  0%{filter:drop-shadow(0 0 6px #d4af37);}
  50%{filter:drop-shadow(0 0 16px #ffd700);}
  100%{filter:drop-shadow(0 0 6px #d4af37);}
}
.logo-container img {
  width:120px;height:120px;border-radius:50%;object-fit:cover;
  border:2px solid #fff;box-shadow:0 0 15px rgba(255,255,255,0.3);
}

.profile-header h3 {
  color:#fffbe7;
  font-weight:600;
  text-shadow:0 0 10px rgba(255,215,0,0.5);
  letter-spacing:0.8px;
}

.avatar-preview img {
  width:110px;height:110px;border-radius:50%;
  border:3px solid #d4af37;
  box-shadow:0 0 20px rgba(212,175,55,0.5);
  transition:transform 0.4s ease,box-shadow 0.4s ease;
}
.avatar-preview img:hover {
  transform:scale(1.1) rotate(3deg);
  box-shadow:0 0 25px rgba(255,230,120,0.8);
}

label {color:#fffbe7;font-weight:600;}
.form-control,.form-select {
  background:rgba(255,255,255,0.06);
  border:1px solid rgba(255,255,255,0.2);
  color:#eee;border-radius:10px;
  transition:all .3s ease;
}
.form-control:focus,.form-select:focus {
  border-color:#ffd700;
  box-shadow:0 0 10px #d4af37;
}

.btn-primary {
  background:linear-gradient(90deg,#b88a00 0%,#fff5d6 50%,#b88a00 100%);
  color:#000;font-weight:600;border:none;border-radius:14px;
  box-shadow:0 0 15px rgba(212,175,55,0.6);
  transition:all .3s ease;
}
.btn-primary:hover {
  transform:translateY(-3px) scale(1.05);
  box-shadow:0 0 25px rgba(255,215,0,0.8);
}
.btn-secondary {
  background:rgba(255,255,255,0.1);
  border:1px solid rgba(212,175,55,0.3);
  color:#fff;border-radius:14px;
}
.btn-secondary:hover {background:rgba(212,175,55,0.2);}
</style>
</head>
<body>

<div class="container mt-5 mb-5">
  <div class="card profile-card p-4 mx-auto" style="max-width:850px;">

    <div class="profile-header text-center">
      <div class="logo-container">
        <img src="uploads/Logo cloud food en claro.png" alt="Logo Cloud Food">
      </div>
      <h3><i class="fa-solid fa-user-pen"></i> Editar Perfil</h3>
    </div>

    <div class="text-center mb-4 avatar-preview">
      <?php if (!empty($cliente['avatar'])): ?>
        <img src="<?= htmlspecialchars($cliente['avatar']) ?>" alt="Avatar">
      <?php else: ?>
        <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Avatar">
      <?php endif; ?>
    </div>

    <form action="perfil.php" method="POST" enctype="multipart/form-data">
      <div class="row mb-3">
        <div class="col-md-6">
          <label>Nombre:</label>
          <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($cliente['nombre']) ?>" required>
        </div>
        <div class="col-md-6">
          <label>Correo:</label>
          <input type="email" name="correo" class="form-control" value="<?= htmlspecialchars($cliente['correo']) ?>" required>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label>Teléfono:</label>
          <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($cliente['telefono']) ?>">
        </div>
        <div class="col-md-6">
          <label>Dirección:</label>
          <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($cliente['direccion']) ?>">
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label>Nueva contraseña:</label>
          <input type="password" name="password" class="form-control" placeholder="Dejar vacío si no desea cambiarla">
        </div>
        <div class="col-md-6">
          <label>Confirmar contraseña:</label>
          <input type="password" name="confirm_password" class="form-control" placeholder="Confirmar nueva contraseña">
        </div>
      </div>
      <div class="mb-3">
        <label>Avatar:</label>
        <input type="file" name="avatar" class="form-control">
      </div>
      <div class="mb-4">
        <label>Sede:</label>
        <select name="sede_id" class="form-select" required>
          <option value="">Seleccione una sede</option>
          <?php while ($fila = $sedes->fetch_assoc()): ?>
            <option value="<?= $fila['id'] ?>" <?= ($fila['id'] == $cliente['sede_id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($fila['nombre']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="text-center">
        <button type="submit" class="btn btn-primary me-2"><i class="fa-solid fa-save"></i> Guardar cambios</button>
        <a href="index_cliente.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Volver</a>
      </div>
    </form>
  </div>
</div>

<script>
<?php if (isset($_GET['updated'])): ?>
Swal.fire({

    icon:'success',
    title:'Perfil actualizado',
    text:'Tus cambios se han guardado correctamente.',
    confirmButtonColor:'#d4af37',
    background:'#1a0f0f',
    color:'#fffbe7',
    showConfirmButton: true,
    confirmButtonText: 'Aceptar'
}).then(() => {
    // 🔁 Recarga automática después del cierre
  
});

<?php elseif ($msg === "error"): ?>
Swal.fire({
    icon:'error',
    title:'Error',
    text:'Ocurrió un problema al actualizar tu perfil.',
    confirmButtonColor:'#d4af37',
    background:'#1a0f0f',
    color:'#fffbe7'
});
<?php elseif ($msg === "noCoincide"): ?>
Swal.fire({
    icon:'warning',
    title:'Contraseñas no coinciden',
    text:'Verifica que ambas contraseñas sean iguales.',
    confirmButtonColor:'#d4af37',
    background:'#1a0f0f',
    color:'#fffbe7'
});
<?php endif; ?>
</script>
</body>
</html>


