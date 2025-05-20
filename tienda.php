<?php
require_once 'includes/config.php';
require_once 'includes/funciones.php';

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

// Obtener información del usuario
$user_id = $_SESSION['user_id'];
$cliente_id = obtenerClienteIDPorUsuarioID($user_id);
$cliente = obtenerCliente($cliente_id);

// Obtener productos
$productos = obtenerProductos();

// Generar catálogo XML (solo una vez o cuando se actualicen productos)
if (!file_exists('catalogo.xml')) {
    generarCatalogoXML();
}

// Manejar añadir al carrito
if (isset($_POST['agregar_carrito'])) {
    $referencia = limpiarDatos($_POST['referencia']);
    $cantidad = intval(limpiarDatos($_POST['cantidad']));
    
    // Inicializar carrito si no existe
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }
    
    // Buscar producto
    $producto_encontrado = null;
    foreach ($productos as $producto) {
        if ($producto['referencia'] === $referencia) {
            $producto_encontrado = $producto;
            break;
        }
    }
    
    if ($producto_encontrado) {
        // Verificar si ya está en el carrito
        $en_carrito = false;
        foreach ($_SESSION['carrito'] as &$item) {
            if ($item['referencia'] === $referencia) {
                $item['cantidad'] += $cantidad;
                $en_carrito = true;
                break;
            }
        }
        
        if (!$en_carrito) {
            $_SESSION['carrito'][] = [
                'referencia' => $referencia,
                'nombre' => $producto_encontrado['nombre'],
                'precio' => $producto_encontrado['precio'],
                'cantidad' => $cantidad
            ];
        }
        
        header('Location: tienda.php?success=Producto+agregado+al+carrito');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Peruana - Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-custom fixed-top">
        <div class="container">
            <!-- Logo + Texto (enlace a inicio) -->
            <a class="navbar-brand d-flex align-items-center" href="tienda.php">
                <img src="img/logo-tienda.png" alt="Logo Tienda Peruana" width="40" height="40" class="me-2">
                Tienda Peruana
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="tienda.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="carrito.php">
                            <i class="bi bi-cart-fill"></i> Carrito
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="catalogo.xml" download>Descargar Catálogo</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <?php echo htmlspecialchars($cliente['nombre']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="perfil.php">Mi perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Cerrar sesión</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="carrito.php">
                            <img src="img/carrito-icono.png" alt="Carrito" width="24" height="24">
                            <span class="badge bg-danger ms-1">
                                <?php echo isset($_SESSION['carrito']) ? count($_SESSION['carrito']) : 0; ?>
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container my-5">
        <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>
        
        <h1 class="mb-4">Productos Peruanos</h1>
        
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($productos as $producto): ?>
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <img src="img/<?php echo htmlspecialchars($producto['referencia']); ?>.jpg" 
                         class="card-img-top" 
                         alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                         onerror="this.src='img/default.jpg'">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                        <p class="h4 text-primary">S/ <?php echo number_format($producto['precio'], 2); ?></p>
                    </div>
                    <div class="card-footer bg-white">
                        <form method="POST" action="tienda.php">
                            <input type="hidden" name="referencia" value="<?php echo htmlspecialchars($producto['referencia']); ?>">
                            <div class="input-group">
                                <input type="number" name="cantidad" class="form-control" value="1" min="1">
                                <button type="submit" name="agregar_carrito" class="btn btn-primary">
                                    Añadir al carrito
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Pie de página -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Tienda Peruana</h5>
                    <p>Promoviendo los productos tradicionales del Perú.</p>
                </div>
                <div class="col-md-3">
                    <h5>Enlaces</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white">Inicio</a></li>
                        <li><a href="#" class="text-white">Productos</a></li>
                        <li><a href="#" class="text-white">Contacto</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Contacto</h5>
                    <address>
                        <p>Lima, Perú<br>
                        info@tiendaperuana.com<br>
                        +51 123 456 789</p>
                    </address>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p>&copy; <?php echo date('Y'); ?> Tienda Peruana. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>