<?php include 'conexion.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>🍽️ Gestión de Platos | Cloud Food</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: linear-gradient(135deg, #fff1eb, #ace0f9);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .container {
            margin-top: 50px;
        }

        .card {
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            animation: fadeInUp 1s ease;
            background: #ffffff;
            padding: 25px;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h2 {
            font-weight: bold;
            color: #333;
        }

        .table img {
            width: 100px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
            transition: transform .3s;
        }

        .table img:hover {
            transform: scale(1.2);
        }

        .btn {
            border-radius: 10px;
            font-weight: bold;
        }

        .btn-gradient {
            background: linear-gradient(135deg, #ff6a00, #ee0979);
            color: white;
            padding: 12px 25px;
            border-radius: 30px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-gradient:hover {
            background: linear-gradient(135deg, #ee0979, #ff6a00);
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
            color: #fff;
        }

        .form-label {
            font-weight: 600;
            color: #444;
        }

        .badge {
            font-size: 0.9rem;
            padding: 8px 12px;
            border-radius: 12px;
        }

        .modal-content {
            border-radius: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
                <!-- Centered large logo for branding -->
                <div style="text-align:center;margin-bottom:6px;">
                    <img src="uploads/Logo_cloud_food_en_oscuro-transparente (1).png" alt="Cloud Food" class="site-logo site-logo--header" loading="lazy" decoding="async">
                </div>
                <h2 class="text-center mb-4">🍴 Gestión de Platos - Cloud Food</h2>

            <!-- Formulario para agregar -->
            <form method="post" enctype="multipart/form-data" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Ej: Ceviche Mixto" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Precio (S/)</label>
                    <input type="number" name="precio" class="form-control" step="0.01" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Categoría</label>
                    <select name="categoria" class="form-select" required>
                        <option>Entrada</option>
                        <option>Plato Principal</option>
                        <option>Postre</option>
                        <option>Bebida</option>
                        <option>Embutidos</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Imagen</label>
                    <input type="file" name="imagen" class="form-control" accept="image/*">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sede</label>
                    <select name="sede" class="form-select" required>
                        <option>Barranca</option>
                        <option>Puerto</option>
                        <option>Pativilca</option>
                        <option>Huacho</option>
                    </select>
                </div>
                <div class="col-12 text-center">
                    <button type="submit" name="guardar" class="btn btn-success px-4">💾 Guardar</button>
                </div>
            </form>
            <hr>

            <!-- PHP FUNCIONAL -->
            <?php
            // Guardar
            if (isset($_POST['guardar'])) {
                $nombre = $_POST['nombre'];
                $precio = $_POST['precio'];
                $categoria = $_POST['categoria'];
                $sede = $_POST['sede'];
                $imagen = "";
                if (!empty($_FILES['imagen']['name'])) {
                    $imagen = "uploads/" . time() . "_" . basename($_FILES['imagen']['name']);
                    move_uploaded_file($_FILES['imagen']['tmp_name'], $imagen);
                }
                $conexion->query("INSERT INTO platos(nombre,precio,categoria,imagen,sede,estado) 
                                  VALUES('$nombre','$precio','$categoria','$imagen','$sede','Disponible')");
                echo "<script>
                    Swal.fire({icon:'success',title:'Plato agregado',text:'$nombre registrado con éxito'})
                    .then(()=> location.href='platos_crud.php');
                </script>";
            }

            // Eliminar
            if (isset($_GET['eliminar'])) {
                $id = intval($_GET['eliminar']);
                $conexion->query("DELETE FROM platos WHERE id=$id");
                echo "<script>
                    Swal.fire({icon:'error',title:'Eliminado',text:'El plato fue eliminado'})
                    .then(()=> location.href='platos_crud.php');
                </script>";
            }

            // Agotar
            if (isset($_GET['agotado'])) {
                $id = intval($_GET['agotado']);
                $conexion->query("UPDATE platos SET estado='Agotado' WHERE id=$id");
                echo "<script>
                    Swal.fire({icon:'warning',title:'Agotado',text:'El plato fue marcado como agotado'})
                    .then(()=> location.href='platos_crud.php');
                </script>";
            }

            // Activar
            if (isset($_GET['activar'])) {
                $id = intval($_GET['activar']);
                $conexion->query("UPDATE platos SET estado='Disponible' WHERE id=$id");
                echo "<script>
                    Swal.fire({icon:'success',title:'Activado',text:'El plato fue reactivado correctamente'})
                    .then(()=> location.href='platos_crud.php');
                </script>";
            }

            // Editar
            if (isset($_POST['editar'])) {
                $id = $_POST['id'];
                $nombre = $_POST['nombre'];
                $precio = $_POST['precio'];
                $categoria = $_POST['categoria'];
                $sede = $_POST['sede'];
                $imagen_sql = "";
                if (!empty($_FILES['imagen']['name'])) {
                    $imagen = "uploads/" . time() . "_" . basename($_FILES['imagen']['name']);
                    move_uploaded_file($_FILES['imagen']['tmp_name'], $imagen);
                    $imagen_sql = ", imagen='$imagen'";
                }
                $conexion->query("UPDATE platos SET nombre='$nombre', precio='$precio', categoria='$categoria', sede='$sede' $imagen_sql WHERE id=$id");
                echo "<script>
                    Swal.fire({icon:'success',title:'Plato editado',text:'Cambios guardados'})
                    .then(()=> location.href='platos_crud.php');
                </script>";
            }
            ?>

            <!-- Tabla de platos -->
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Categoría</th>
                            <th>Sede</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $res = $conexion->query("SELECT * FROM platos ORDER BY id DESC");
                        while ($row = $res->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td><img src='" . $row['imagen'] . "' alt='img'></td>";
                            echo "<td>" . $row['nombre'] . "</td>";
                            echo "<td>S/ " . $row['precio'] . "</td>";
                            echo "<td>" . $row['categoria'] . "</td>";
                            echo "<td><span class='badge bg-info text-dark'>" . $row['sede'] . "</span></td>";
                            echo "<td><span class='badge " . ($row['estado'] == 'Disponible' ? 'bg-success' : 'bg-danger') . "'>" . $row['estado'] . "</span></td>";
                            echo "<td>";

                            // Botones dinámicos
                            echo "<button class='btn btn-primary btn-sm me-1' data-bs-toggle='modal' data-bs-target='#editModal" . $row['id'] . "'>✏️ Editar</button>";

                            if ($row['estado'] == 'Disponible') {
                                echo "<a href='?agotado=" . $row['id'] . "' class='btn btn-warning btn-sm me-1'>⚠️ Agotar</a>";
                            } else {
                                echo "<a href='?activar=" . $row['id'] . "' class='btn btn-success btn-sm me-1'>✅ Activar</a>";
                            }

                            echo "<a href='?eliminar=" . $row['id'] . "' class='btn btn-danger btn-sm'>🗑️ Eliminar</a>";
                            echo "</td></tr>";

                            // Modal de edición
                            echo "
                            <div class='modal fade' id='editModal" . $row['id'] . "' tabindex='-1'>
                                <div class='modal-dialog'>
                                    <div class='modal-content'>
                                        <form method='post' enctype='multipart/form-data'>
                                            <div class='modal-header bg-dark text-white'>
                                                <h5 class='modal-title'>Editar " . $row['nombre'] . "</h5>
                                                <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                            </div>
                                            <div class='modal-body'>
                                                <input type='hidden' name='id' value='" . $row['id'] . "'>
                                                <div class='mb-3'>
                                                    <label class='form-label'>Nombre</label>
                                                    <input type='text' name='nombre' class='form-control' value='" . $row['nombre'] . "' required>
                                                </div>
                                                <div class='mb-3'>
                                                    <label class='form-label'>Precio</label>
                                                    <input type='number' name='precio' class='form-control' value='" . $row['precio'] . "' step='0.01' required>
                                                </div>
                                                <div class='mb-3'>
                                                    <label class='form-label'>Categoría</label>
                                                    <select name='categoria' class='form-select'>
                                                        <option " . ($row['categoria'] == 'Entrada' ? 'selected' : '') . ">Entrada</option>
                                                        <option " . ($row['categoria'] == 'Plato Principal' ? 'selected' : '') . ">Plato Principal</option>
                                                        <option " . ($row['categoria'] == 'Postre' ? 'selected' : '') . ">Postre</option>
                                                        <option " . ($row['categoria'] == 'Bebida' ? 'selected' : '') . ">Bebida</option>
                                                        <option " . ($row['categoria'] == 'Embutidos' ? 'selected' : '') . ">Embutidos</option>
                                                    </select>
                                                </div>
                                                <div class='mb-3'>
                                                    <label class='form-label'>Sede</label>
                                                    <select name='sede' class='form-select'>
                                                        <option " . ($row['sede'] == 'Barranca' ? 'selected' : '') . ">Barranca</option>
                                                        <option " . ($row['sede'] == 'Puerto' ? 'selected' : '') . ">Puerto</option>
                                                        <option " . ($row['sede'] == 'Pativilca' ? 'selected' : '') . ">Pativilca</option>
                                                        <option " . ($row['sede'] == 'Huacho' ? 'selected' : '') . ">Huacho</option>
                                                    </select>
                                                </div>
                                                <div class='mb-3'>
                                                    <label class='form-label'>Imagen (opcional)</label>
                                                    <input type='file' name='imagen' class='form-control' accept='image/*'>
                                                    <img src='" . $row['imagen'] . "' class='mt-2 rounded shadow' width='100'>
                                                </div>
                                            </div>
                                            <div class='modal-footer'>
                                                <button type='submit' name='editar' class='btn btn-success'>Guardar cambios</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="text-center mt-4">
                <button id="btnVolver" class="btn btn-lg btn-gradient">
                    ⬅️ Volver al Inicio
                </button>
            </div>
        </div>
    </div>

    <!-- Librerías -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sonido tipo iPhone -->
    <audio id="soundNotif" preload="auto">
        <source src="https://assets.mixkit.co/sfx/preview/mixkit-software-interface-ding-2574.mp3" type="audio/mpeg">
    </audio>

    <script>
        document.getElementById("btnVolver").addEventListener("click", function() {
            document.getElementById("soundNotif").play();
            Swal.fire({
                title: "🔄 Redirigiendo...",
                text: "Volviendo al panel de pedidos de Cloud Food",
                icon: "info",
                timer: 2000,
                showConfirmButton: false,
                allowOutsideClick: false,
                didClose: () => {
                    window.location.href = "panel_trabajador.php";
                }
            });
        });
    </script>
</body>

</html>
