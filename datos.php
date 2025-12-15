<?php
session_start();

include_once 'db.php';

$database = new Database();
$conn = $database->getConnection();

$categorias = array(
    'pesas' => '🏋️ Pesas',
    'boxeo' => '🥊 Boxeo',
    'cardio' => '🏃 Cardio',
    'yoga' => '🧘 Yoga',
    'suplementos' => '💊 Suplementos',
    'ropa' => '👕 Ropa Deportiva'
);

function obtenerProductos($conn, $categoria_filtro = null) {
    $query = "SELECT * FROM productos WHERE activo = 1";
    
    if ($categoria_filtro) {
        $query .= " AND categoria = :categoria";
    }
    $query .= " ORDER BY fecha_creacion DESC";
    
    $stmt = $conn->prepare($query);
    
    if ($categoria_filtro) {
        $stmt->bindParam(':categoria', $categoria_filtro);
    }
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerProductoPorId($conn, $id) {
    $query = "SELECT * FROM productos WHERE id = :id AND activo = 1";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

$productos_count = $conn->query("SELECT COUNT(*) as total FROM productos")->fetch(PDO::FETCH_ASSOC);
if ($productos_count['total'] == 0) {
    $sql = "INSERT INTO productos (nombre, descripcion, precio, categoria, stock, imagen) VALUES 
            ('Mancuernas 10kg', 'Mancuernas profesionales de acero', 45.99, 'pesas', 15, 'mancuernas.jpg'),
            ('Guantes de Boxeo', 'Guantes de cuero sintético profesional', 39.99, 'boxeo', 8, 'guantes.jpg')";
    $conn->exec($sql);
}

$productos_array = obtenerProductos($conn);
$productos = array();
foreach ($productos_array as $producto) {
    $productos[$producto['id']] = $producto;
}

$usuario_logueado = false;
$usuario_nombre = '';
$usuario_rol = '';
$usuario_id = 0;
$usuario_email = '';

if (isset($_SESSION['usuario_id'])) {
    $query = "SELECT * FROM usuarios WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $_SESSION['usuario_id']);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        $usuario_logueado = true;
        $usuario_id = $usuario['id'];
        $usuario_nombre = $usuario['nombre'];
        $usuario_rol = $usuario['rol'];
        $usuario_email = $usuario['email'];
    } else {
        unset($_SESSION['usuario_id']);
        unset($_SESSION['usuario_nombre']);
        unset($_SESSION['usuario_rol']);
    }
}

$total_carrito = 0;
if (isset($_SESSION['carrito']) && is_array($_SESSION['carrito'])) {
    $total_carrito = array_sum($_SESSION['carrito']);
}
?>
