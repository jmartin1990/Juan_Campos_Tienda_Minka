<?php
require_once 'includes/config.php';
require_once 'includes/funciones.php';

// Verificar sesión y carrito
if (!isset($_SESSION['user_id']) || empty($_SESSION['carrito'])) {
    header('Location: login.html');
    exit;
}

$user_id = $_SESSION['user_id'];
$cliente_id = obtenerClienteIDPorUsuarioID($user_id);  // Ojo aquí, obtener cliente_id

if (!$cliente_id) {
    header('Location: carrito.php?error=No+se+encontró+cliente+asociado');
    exit;
}

$compra_id = registrarCompra($cliente_id, $_SESSION['carrito']);

if ($compra_id) {
    unset($_SESSION['carrito']);
    header('Location: tienda.php?success=Compra+realizada+con+éxito.+Número+de+pedido:+' . $compra_id);
    exit;
} else {
    header('Location: carrito.php?error=Error+al+procesar+la+compra');
    exit;
}
?>