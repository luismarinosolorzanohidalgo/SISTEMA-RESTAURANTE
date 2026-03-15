<?php
// ----------------------------
// conexion.php incluida
// ----------------------------
$conn = new mysqli("localhost", "root", "", "restaurante_db");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
$conn->set_charset("utf8");

// ----------------------------
// Sesión y usuario
// ----------------------------
session_start();
$clienteLogeado = isset($_SESSION['cliente']);
$trabajadorLogeado = isset($_SESSION['trabajador']);
$nombreUsuario = $clienteLogeado ? $_SESSION['cliente'] : ($trabajadorLogeado ? $_SESSION['trabajador'] : null);

// ----------------------------
// Categorías
// ----------------------------
$categorias = ["Entrada", "Plato Principal", "Postre", "Bebida", "Embutidos"];

// ----------------------------
// Función para obtener platos
// ----------------------------
function obtenerPlatos($conn, $categoria)
{
    if (!$conn) {
        die("Conexión a la base de datos no disponible");
    }
    $stmt = $conn->prepare("SELECT * FROM platos WHERE categoria = ? AND estado = 'Disponible'");
    if (!$stmt) {
        die("Error en la consulta: " . $conn->error);
    }
    $stmt->bind_param("s", $categoria);
    $stmt->execute();
    return $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🍽️ Menú del Restaurante Premium</title>

    <!-- CSS externos -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #6a11cb;
            --secondary-color: #2575fc;
            --accent-color: #f39c12;
            --text-dark: #2c3e50;
            --text-light: #7f8c8d;
            --bg-light: #f5f7fa;
            --white: #ffffff;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --shadow-hover: 0 20px 40px rgba(0, 0, 0, 0.15);
            --border-radius: 20px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--bg-light) 0%, #e0e7ff 100%);
            color: var(--text-dark);
            line-height: 1.6;
            overflow-x: hidden;
            margin: 0;
        }

        /* Animaciones */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease forwards;
        }

        /* Header */
        header {
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--white);
            border-bottom-left-radius: var(--border-radius);
            border-bottom-right-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }

        header h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        header p {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        /* Categorías */
        .nav-categorias {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
            margin: 30px 20px;
            position: sticky;
            top: 0;
            background: var(--white);
            padding: 15px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            z-index: 10;
        }

        .nav-btn {
            background: none;
            border: 2px solid transparent;
            padding: 10px 20px;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
            color: var(--text-light);
        }

        .nav-btn.active {
            background: var(--primary-color);
            color: var(--white);
            border-color: var(--primary-color);
            box-shadow: var(--shadow);
        }

        .nav-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
            color: var(--primary-color);
        }

        /* Contenedor y grid */
        .menu-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        h2 {
            margin: 40px 0 20px;
            color: var(--text-dark);
            font-size: 1.8rem;
            font-weight: 600;
            border-left: 6px solid var(--primary-color);
            padding-left: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        /* Cards */
        .card {
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: var(--transition);
            position: relative;
            opacity: 0;
            transform: translateY(20px);
        }

        .card.animate {
            opacity: 1;
            transform: translateY(0);
            animation: fadeInUp 0.6s ease forwards;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        }

        .card:hover {
            transform: translateY(-10px) rotate(1deg);
            box-shadow: var(--shadow-hover);
        }

        .card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: var(--transition);
        }

        .card:hover img {
            transform: scale(1.05);
        }

        .card-content {
            padding: 20px;
        }

        .card h3 {
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card p.precio {
            font-weight: 700;
            color: var(--accent-color);
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .card p.descripcion {
            color: var(--text-light);
            font-size: 0.95rem;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        /* Botones */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--white);
            padding: 12px 24px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(39, 174, 96, 0.4);
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn:active::before {
            width: 300px;
            height: 300px;
        }

        /* No productos */
        .no-productos {
            color: var(--text-light);
            font-style: italic;
            text-align: center;
            margin: 20px 0;
            padding: 20px;
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        /* Footer */
        footer {
            background: var(--text-dark);
            color: var(--white);
            text-align: center;
            padding: 20px;
            margin-top: 50px;
        }

        /* Volver */
        .btn-volver {
            position: fixed;
            bottom: 20px;
            right: 20px;
            border-radius: 50px;
            padding: 12px 25px;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .btn-volver:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.3);
        }

        /* Responsivo */
        @media (max-width:768px) {
            header h1 {
                font-size: 2.5rem;
            }

            .menu-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .nav-categorias {
                flex-direction: column;
                align-items: center;
            }

            .nav-btn {
                width: 100%;
                max-width: 200px;
            }
        }

        /* Ocultar secciones no activas */
        .categoria-section {
            display: none;
        }

        .categoria-section.active {
            display: block;
        }
    </style>
</head>

<body>

    <header class="fade-in-up">
        <h1><i class="fas fa-utensils"></i> Menú del Restaurante Premium</h1>
        <p>Descubre sabores inolvidables con nuestra selección exquisita. ¡Haz tu pedido ahora!</p>
    </header>

    <nav class="nav-categorias">
        <?php foreach ($categorias as $index => $cat):
            $icono = ($index === 0) ? 'cheese-wedge' : (($index === 1) ? 'drumstick-bite' : (($index === 2) ? 'ice-cream' : 'cocktail'));
        ?>
            <button class="nav-btn <?= $index === 0 ? 'active' : '' ?>" data-categoria="<?= strtolower(str_replace(' ', '-', $cat)) ?>">
                <i class="fas fa-<?= $icono ?>"></i> <?= $cat ?>
            </button>
        <?php endforeach; ?>
    </nav>

    <div class="menu-container">
        <?php foreach ($categorias as $index => $cat):
            $result = obtenerPlatos($conn, $cat);
            $seccionId = strtolower(str_replace(' ', '-', $cat));
        ?>
            <section id="seccion-<?= $seccionId ?>" class="categoria-section <?= $index === 0 ? 'active' : '' ?>">
                <h2 class="fade-in-up"><i class="fas fa-<?= ($index === 0) ? 'cheese-wedge' : (($index === 1) ? 'drumstick-bite' : (($index === 2) ? 'ice-cream' : 'cocktail')) ?>"></i> <?= $cat ?></h2>
                <div class="menu-grid">
                    <?php if ($result->num_rows > 0):
                        $contador = 0;
                        while ($row = $result->fetch_assoc()): $contador++;
                    ?>
                            <div class="card fade-in-up" style="animation-delay: <?= $contador * 0.1 ?>s;">
                                <img src="<?= htmlspecialchars($row['imagen']) ?>" alt="<?= htmlspecialchars($row['nombre']) ?>" loading="lazy" onerror="this.src='https://via.placeholder.com/280x200?text=Imagen+no+disponible'">
                                <div class="card-content">
                                    <h3><i class="fas fa-star" style="color: var(--accent-color);"></i> <?= htmlspecialchars($row['nombre']) ?></h3>
                                    <p class="descripcion"><?= isset($row['descripcion']) ? htmlspecialchars($row['descripcion']) : 'Un plato delicioso y fresco.' ?></p>
                                    <p class="precio">S/ <?= number_format($row['precio'], 2) ?></p>
                                    <a href="pedido.php?id=<?= $row['id'] ?>" class="btn pedir-btn" data-nombre="<?= htmlspecialchars($row['nombre']) ?>"><i class="fas fa-shopping-cart"></i> Pedir Ahora</a>
                                </div>
                            </div>
                        <?php endwhile;
                    else: ?>
                        <p class="no-productos"><i class="fas fa-exclamation-triangle"></i> No hay productos disponibles en esta categoría.</p>
                    <?php endif; ?>
                </div>
            </section>
        <?php endforeach; ?>
        <button class="btn btn-danger btn-volver" onclick="volverIndex()">⬅ Volver</button>
    </div>

    <footer>
        <p>&copy; 2025 Restaurante Premium. Todos los derechos reservados. <i class="fas fa-heart" style="color: #e74c3c;"></i></p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function volverIndex() {
            Swal.fire({
                title: '¿Quieres volver al inicio?',
                text: "Serás redirigido al menú principal.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, volver',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "index_trabajador.php";
                }
            });
        }

        // Tabs categorías
        document.querySelectorAll('.nav-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const categoria = this.dataset.categoria;
                document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                document.querySelectorAll('.categoria-section').forEach(sec => sec.classList.remove('active'));
                document.getElementById(`seccion-${categoria}`).classList.add('active');
                setTimeout(() => {
                    document.querySelectorAll('.card').forEach((card, index) => {
                        card.style.animationDelay = `${index*0.1}s`;
                        card.classList.add('animate');
                    });
                }, 100);
            });
        });

        // Pedir botón con SweetAlert
        document.querySelectorAll('.pedir-btn').forEach(boton => {
            boton.addEventListener('click', function(event) {
                event.preventDefault();
                const url = this.getAttribute('href');
                const nombre = this.dataset.nombre;
                Swal.fire({
                    title: `<i class="fas fa-utensils" style="color: var(--primary-color);"></i> Confirmar Pedido`,
                    html: `¿Deseas agregar <strong>"${nombre}"</strong> a tu pedido?`,
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonColor: "var(--primary-color)",
                    cancelButtonColor: "#d33",
                    confirmButtonText: '<i class="fas fa-check"></i> Sí, agregar',
                    cancelButtonText: '<i class="fas fa-times"></i> Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let carrito = JSON.parse(localStorage.getItem('carrito') || '[]');
                        carrito.push({
                            nombre,
                            id: url.split('=')[1]
                        });
                        localStorage.setItem('carrito', JSON.stringify(carrito));
                        Swal.fire({
                            title: '¡Pedido agregado!',
                            icon: 'success',
                            timer: 2000,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false
                        });
                        setTimeout(() => {
                            window.location.href = url;
                        }, 2100);
                    }
                });
            });
        });
    </script>
</body>

</html>