<?php
require_once 'includes/config.php';
require_once 'includes/funciones.php';

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actual = limpiarDatos($_POST['actual']);
    $nueva = limpiarDatos($_POST['nueva']);
    $confirmar = limpiarDatos($_POST['confirmar']);
    
    // Validaciones
    if (empty($actual) || empty($nueva) || empty($confirmar)) {
        $error = "Todos los campos son obligatorios";
    } elseif ($nueva !== $confirmar) {
        $error = "Las nuevas contraseñas no coinciden";
    } elseif (strlen($nueva) < 8) {
        $error = "La contraseña debe tener al menos 8 caracteres";
    } else {
        // Verificar contraseña actual
        $stmt = $conn->prepare("SELECT contrasena FROM usuarios WHERE id = ?");
        $stmt->execute([$user_id]);
        $usuario = $stmt->fetch();
        
        if ($usuario && password_verify($actual, $usuario['contrasena'])) {
            // Actualizar contraseña
            $nueva_hash = password_hash($nueva, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE usuarios SET contrasena = ? WHERE id = ?");
            if ($stmt->execute([$nueva_hash, $user_id])) {
                $success = "Contraseña actualizada correctamente";
                
                // Opcional: Cerrar sesión en todos los dispositivos
                // $_SESSION = array();
                // session_destroy();
                // header('Location: login.html?success=Contraseña+actualizada.+Inicia+sesión+de+nuevo');
                // exit;
            } else {
                $error = "Error al actualizar la contraseña";
            }
        } else {
            $error = "La contraseña actual es incorrecta";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña - Tienda Peruana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>
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
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-custom text-white">
                        <h3 class="mb-0">Cambiar Contraseña</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="cambiar_password.php">
                            <div class="mb-3">
                                <label for="actual" class="form-label">Contraseña Actual</label>
                                <input type="password" class="form-control" id="actual" name="actual" required>
                            </div>
                            <div class="mb-3">
                                <label for="nueva" class="form-label">Nueva Contraseña</label>
                                <input type="password" class="form-control" id="nueva" name="nueva" required>
                                <div class="form-text">Mínimo 8 caracteres</div>
                            </div>
                            <div class="mb-3">
                                <label for="confirmar" class="form-label">Confirmar Nueva Contraseña</label>
                                <input type="password" class="form-control" id="confirmar" name="confirmar" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
                                <a href="perfil.php" class="btn btn-outline-secondary">Volver al perfil</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
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