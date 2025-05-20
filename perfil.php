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

// Procesar actualización de perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos_actualizados = [
        'nombre' => limpiarDatos($_POST['nombre']),
        'apellidos' => limpiarDatos($_POST['apellidos']),
        'correo' => limpiarDatos($_POST['correo']),
        'fecha_nacimiento' => limpiarDatos($_POST['fecha_nacimiento']),
        'genero' => limpiarDatos($_POST['genero'])
    ];
    
    if (actualizarCliente($cliente_id, $datos_actualizados)) {
        $mensaje = "Perfil actualizado correctamente";
        $cliente = array_merge($cliente, $datos_actualizados);
    } else {
        $error = "Error al actualizar el perfil";
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
    <nav class="navbar navbar-expand-lg navbar-dark bg-custom">
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
    
    <div class="container my-5">
        <h1>Mi Perfil</h1>
        
        <?php if (isset($mensaje)): ?>
        <div class="alert alert-success"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="perfil.php">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre</label>
                    <input type="text" class="form-control" name="nombre" 
                           value="<?php echo htmlspecialchars($cliente['nombre']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Apellidos</label>
                    <input type="text" class="form-control" name="apellidos" 
                           value="<?php echo htmlspecialchars($cliente['apellidos']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" class="form-control" name="correo" 
                           value="<?php echo htmlspecialchars($cliente['correo']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Fecha de nacimiento</label>
                    <input type="date" class="form-control" name="fecha_nacimiento" 
                           value="<?php echo htmlspecialchars($cliente['fecha_nacimiento']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Género</label>
                    <select class="form-select" name="genero">
                        <option value="M" <?php echo ($cliente['genero'] == 'M') ? 'selected' : ''; ?>>Masculino</option>
                        <option value="F" <?php echo ($cliente['genero'] == 'F') ? 'selected' : ''; ?>>Femenino</option>
                        <option value="O" <?php echo ($cliente['genero'] == 'O') ? 'selected' : ''; ?>>Otro</option>
                    </select>
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                <a href="cambiar_password.php" class="btn btn-outline-secondary">Cambiar contraseña</a>
            </div>
        </form>
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