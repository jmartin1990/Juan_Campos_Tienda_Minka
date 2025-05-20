<?php
require_once 'includes/config.php';
require_once 'includes/funciones.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = limpiarDatos($_POST['usuario']);
    $contrasena = limpiarDatos($_POST['contrasena']);
    
    if (empty($usuario) || empty($contrasena)) {
        header('Location: login.html?error=Usuario+y+contraseña+son+obligatorios');
        exit;
    }
    
    $user_id = verificarCredenciales($usuario, $contrasena);
    
    if ($user_id) {
        session_regenerate_id();
        $_SESSION['user_id'] = $user_id;
        header('Location: tienda.php');
        exit;
    } else {
        header('Location: login.html?error=Credenciales+incorrectas');
        exit;
    }
} else {
    header('Location: login.html');
    exit;
}
?>