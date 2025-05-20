<?php
$host = '127.0.0.1'; // Usar IP es más confiable
$port = 3307; // PUERTO ESPECIFICADO EN my.ini
$dbname = 'tienda_peruana';
$username = 'tienda_user';
$password = 'Lapagol191590*';

try {
    $conn = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    echo "¡Conexión exitosa!";
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>