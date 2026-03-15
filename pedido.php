<?php include 'conexion.php'; ?>
<?php
if (!isset($_GET['id'])) {
    die("❌ Error: ID de plato no válido.");
}

$id_plato = intval($_GET['id']);
$id_cliente = 3; // Cliente existente (Yamile Laveriano)

$sql = "SELECT nombre, precio FROM platos WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_plato);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("❌ El plato no existe.");
}
$plato = $result->fetch_assoc();
$stmt->close();

$total = $plato['precio'];
$nombrePlato = $plato['nombre'];

// Insertar pedido
$stmt = $conexion->prepare("INSERT INTO pedidos (id_cliente, total) VALUES (?, ?)");
$stmt->bind_param("id", $id_cliente, $total);

if ($stmt->execute()) {
    $status = "success";
    $msg = "Tu pedido de '" . $nombrePlato . "' fue registrado con éxito";
} else {
    $status = "error";
    $msg = "Hubo un error al registrar el pedido: " . $stmt->error;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Pedido Cloud Food</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">

<script>
document.addEventListener("DOMContentLoaded", () => {
  Swal.fire({
    title: "<?php echo ($status == 'success') ? '🎉 Pedido Realizado' : '⚠️ Error'; ?>",
    text: "<?php echo $msg; ?>",
    icon: "<?php echo $status; ?>",
    confirmButtonText: "🍴 Volver al Menú",
    confirmButtonColor: "#27ae60",
    backdrop: `
      rgba(0,0,0,0.4)
      url("https://media.giphy.com/media/3o7TKMt1VVNkHV2PaE/giphy.gif")
      center top
      no-repeat
    `
  }).then(() => {
    window.location.href = "index_cliente.php";
  });
});
</script>

</body>
</html>
