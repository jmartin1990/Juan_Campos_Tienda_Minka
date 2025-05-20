<?php
require_once 'includes/config.php';
require_once 'includes/funciones.php';

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

// Obtener datos del cliente
$user_id = $_SESSION['user_id'];
$cliente_id = obtenerClienteIDPorUsuarioID($user_id);
$cliente = obtenerCliente($cliente_id);

// Manejar eliminación de productos del carrito
if (isset($_GET['eliminar'])) {
    $referencia = limpiarDatos($_GET['eliminar']);
    
    foreach ($_SESSION['carrito'] as $key => $item) {
        if ($item['referencia'] === $referencia) {
            unset($_SESSION['carrito'][$key]);
            break;
        }
    }
    
    // Reindexar array
    $_SESSION['carrito'] = array_values($_SESSION['carrito']);
    header('Location: carrito.php?success=Producto+eliminado+del+carrito');
    exit;
}

// Manejar actualización de cantidades
if (isset($_POST['actualizar'])) {
    foreach ($_POST['cantidades'] as $referencia => $cantidad) {
        $cantidad = intval(limpiarDatos($cantidad));
        
        if ($cantidad <= 0) {
            continue;
        }
        
        foreach ($_SESSION['carrito'] as &$item) {
            if ($item['referencia'] === $referencia) {
                $item['cantidad'] = $cantidad;
                break;
            }
        }
    }
    
    header('Location: carrito.php?success=Carrito+actualizado');
    exit;
}

// Calcular total
$total = 0;
if (isset($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $item) {
        $total += $item['precio'] * $item['cantidad'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Peruana - Carrito</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>
    <main>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-custom">
        <div class="container">
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
                        <a class="nav-link" href="carrito.php">Carrito</a>
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
    </main>
    <!-- Contenido principal -->
    <div class="container my-5">
        <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>
        
        <h1 class="mb-4">Mi Carrito</h1>
        
        <?php if (empty($_SESSION['carrito'])): ?>
        <div class="alert alert-info">
            Tu carrito está vacío. <a href="tienda.php" class="alert-link">Ver productos</a>
        </div>
        <?php else: ?>
        <form method="POST" action="carrito.php">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio unitario</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['carrito'] as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                            <td>S/ <?php echo number_format($item['precio'], 2); ?></td>
                            <td>
                                <input type="number" name="cantidades[<?php echo htmlspecialchars($item['referencia']); ?>]" 
                                       value="<?php echo htmlspecialchars($item['cantidad']); ?>" 
                                       min="1" class="form-control" style="width: 80px;">
                            </td>
                            <td>S/ <?php echo number_format($item['precio'] * $item['cantidad'], 2); ?></td>
                            <td>
                                <a href="carrito.php?eliminar=<?php echo htmlspecialchars($item['referencia']); ?>" 
                                   class="btn btn-sm btn-danger">
                                    Eliminar
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="table-active">
                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                            <td colspan="2"><strong>S/ <?php echo number_format($total, 2); ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="tienda.php" class="btn btn-outline-primary">Seguir comprando</a>
                <div class="d-flex gap-2">
                    <button type="submit" name="actualizar" class="btn btn-secondary">
                        Actualizar carrito
                    </button>
                    <a href="procesar_compra.php" class="btn btn-primary">
                        Proceder al pago
                    </a>
                </div>
            </div>
        </form>
        <?php endif; ?>
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