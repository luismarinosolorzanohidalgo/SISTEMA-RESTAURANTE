<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "conexion.php"; // Asegúrate de que este archivo exista y conecte bien

header('Content-Type: application/json');

$id_restaurante = 1; // id fijo o puedes tomarlo de sesión

$sql = "SELECT disponible FROM restaurantes WHERE id = $id_restaurante";
$res = $conexion->query($sql);

if ($res && $res->num_rows > 0) {
  $row = $res->fetch_assoc();
  $nuevo_estado = $row['disponible'] ? 0 : 1;

  $update = "UPDATE restaurantes SET disponible = $nuevo_estado WHERE id = $id_restaurante";
  if ($conexion->query($update)) {
    echo json_encode([
      "success" => true,
      "mensaje" => $nuevo_estado ? "✅ Restaurante activado" : "🚫 Restaurante desactivado"
    ]);
  } else {
    echo json_encode(["success" => false, "error" => $conexion->error]);
  }
} else {
  echo json_encode(["success" => false, "error" => "No se encontró el restaurante"]);
}
?>
