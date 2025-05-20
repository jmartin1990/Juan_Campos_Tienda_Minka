<?php
require_once 'database.php';

// Función para registrar un nuevo usuario
function registrarUsuario($usuario, $contrasena, $cliente_id) {
    global $conn;
    
    $hashed_password = password_hash($contrasena, PASSWORD_BCRYPT);
    
    $stmt = $conn->prepare("INSERT INTO usuarios (usuario, contrasena, cliente_id) VALUES (?, ?, ?)");
    return $stmt->execute([$usuario, $hashed_password, $cliente_id]);
}

// Función para verificar credenciales
function verificarCredenciales($usuario, $contrasena) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT id, contrasena FROM usuarios WHERE usuario = ?");
    $stmt->execute([$usuario]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($contrasena, $user['contrasena'])) {
        return $user['id'];
    }
    return false;
}

// Función para obtener información del cliente
function obtenerCliente($cliente_id) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT * FROM clientes WHERE id = ?");
    $stmt->execute([$cliente_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Función para registrar un nuevo cliente
function registrarCliente($datos) {
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO clientes (nombre, apellidos, correo, fecha_nacimiento, genero) 
                          VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $datos['nombre'],
        $datos['apellidos'],
        $datos['correo'],
        $datos['fecha_nacimiento'],
        $datos['genero']
    ]);
    
    return $conn->lastInsertId();
}

// Función para obtener todos los productos
function obtenerProductos() {
    global $conn;
    
    $stmt = $conn->query("SELECT * FROM productos");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para registrar una compra
function registrarCompra($cliente_id, $carrito) {
    global $conn;
    
    try {
        $conn->beginTransaction();
        
        // Calcular total
        $total = 0;
        foreach ($carrito as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }
        
        // Insertar compra
        $stmt = $conn->prepare("INSERT INTO compras (cliente_id, total) VALUES (?, ?)");
        $stmt->execute([$cliente_id, $total]);
        $compra_id = $conn->lastInsertId();
        
        // Insertar detalles
        foreach ($carrito as $item) {
            $stmt = $conn->prepare("INSERT INTO detalles_compra 
                                   (compra_id, producto_ref, cantidad, precio_unitario) 
                                   VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $compra_id,
                $item['referencia'],
                $item['cantidad'],
                $item['precio']
            ]);
        }
        
        $conn->commit();
        return $compra_id;
    } catch (Exception $e) {
        $conn->rollBack();
        return false;
    }
}

// Función para generar el catálogo en XML
function generarCatalogoXML() {
    $productos = obtenerProductos();
    
    $xml = new DOMDocument('1.0', 'UTF-8');
    $xml->formatOutput = true;
    
    $catalogo = $xml->createElement('catalogo');
    $xml->appendChild($catalogo);
    
    foreach ($productos as $producto) {
        $item = $xml->createElement('producto');
        $catalogo->appendChild($item);
        
        foreach ($producto as $key => $value) {
            $element = $xml->createElement($key, htmlspecialchars($value));
            $item->appendChild($element);
        }
    }
    
    $xml->save('catalogo.xml');
    return file_exists('catalogo.xml');
}

function limpiarDatos($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function actualizarCliente($id, $datos) {
    global $conn;
    
    $stmt = $conn->prepare("UPDATE clientes SET 
                          nombre = ?, 
                          apellidos = ?, 
                          correo = ?, 
                          fecha_nacimiento = ?, 
                          genero = ?
                          WHERE id = ?");
    
    return $stmt->execute([
        $datos['nombre'],
        $datos['apellidos'],
        $datos['correo'],
        $datos['fecha_nacimiento'],
        $datos['genero'],
        $id
    ]);
}
function obtenerClienteIDPorUsuarioID($usuario_id) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT cliente_id FROM usuarios WHERE id = ?");
    $stmt->execute([$usuario_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $row ? $row['cliente_id'] : false;
}

?>