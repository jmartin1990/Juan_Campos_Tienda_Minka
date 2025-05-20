<?php
require_once 'includes/funciones.php';
require_once 'includes/config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar y procesar el formulario
    $datos = [
        'nombre' => limpiarDatos($_POST['nombre']),
        'apellidos' => limpiarDatos($_POST['apellidos']),
        'correo' => limpiarDatos($_POST['correo']),
        'fecha_nacimiento' => limpiarDatos($_POST['fecha_nacimiento']),
        'genero' => limpiarDatos($_POST['genero']),
    ];
    
    $usuario = limpiarDatos($_POST['usuario']);
    $contrasena = limpiarDatos($_POST['contrasena']);
    $confirmar_contrasena = limpiarDatos($_POST['confirmar_contrasena']);
    
    // Validaciones básicas
    if (empty($usuario) || empty($contrasena) || empty($datos['nombre']) || empty($datos['correo'])) {
        $error = 'Los campos marcados con * son obligatorios';
    } elseif ($contrasena !== $confirmar_contrasena) {
        $error = 'Las contraseñas no coinciden';
    } else {
        try {
            // Registrar cliente
            $cliente_id = registrarCliente($datos);
            
            // Registrar usuario
            if (registrarUsuario($usuario, $contrasena, $cliente_id)) {
                $success = 'Registro exitoso. Ahora puedes iniciar sesión.';
                header('Location: login.html?success=' . urlencode($success));
                exit;
            } else {
                $error = 'Error al registrar el usuario. Inténtalo de nuevo.';
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = 'El usuario o correo electrónico ya está registrado';
            } else {
                $error = 'Error en el registro: ' . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Peruana - Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow">
                    <div class="card-header bg-custom text-white text-center">
                        <a><img src="img/logo-tienda.png" alt="Logo Tienda Peruana" width="150" height="50" class="me-2"></a>
                        <h3>Registro de Usuario</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form action="registro.php" method="POST">
                            <h4 class="mb-3">Datos de acceso</h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="usuario" class="form-label">Usuario *</label>
                                    <input type="text" class="form-control" id="usuario" name="usuario" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="correo" class="form-label">Correo electrónico *</label>
                                    <input type="email" class="form-control" id="correo" name="correo" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="contrasena" class="form-label">Contraseña *</label>
                                    <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="confirmar_contrasena" class="form-label">Confirmar contraseña *</label>
                                    <input type="password" class="form-control" id="confirmar_contrasena" name="confirmar_contrasena" required>
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <h4 class="mb-3">Datos personales</h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nombre" class="form-label">Nombre *</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="apellidos" class="form-label">Apellidos</label>
                                    <input type="text" class="form-control" id="apellidos" name="apellidos">
                                </div>
                                <div class="col-md-6">
                                    <label for="fecha_nacimiento" class="form-label">Fecha de nacimiento</label>
                                    <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Género</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="genero" id="genero_m" value="M">
                                            <label class="form-check-label" for="genero_m">Masculino</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="genero" id="genero_f" value="F">
                                            <label class="form-check-label" for="genero_f">Femenino</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="genero" id="genero_o" value="O" checked>
                                            <label class="form-check-label" for="genero_o">Otro</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary">Registrarse</button>
                                <a href="login.html" class="btn btn-outline-secondary">Volver al login</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>