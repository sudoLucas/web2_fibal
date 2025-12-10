<?php
session_start();

if (!isset($_SESSION['productos'])) {

    $_SESSION['productos'] = array();
    
    $_SESSION['productos'][1] = array(
        'id' => 1,
        'nombre' => 'Mancuernas 10kg',
        'descripcion' => 'Mancuernas profesionales de acero',
        'precio' => 45.99,
        'categoria' => 'pesas',
        'stock' => 15,
        'imagen' => 'mancuernas.jpg'
    );
    
    $_SESSION['productos'][2] = array(
        'id' => 2,
        'nombre' => 'Guantes de Boxeo',
        'descripcion' => 'Guantes de cuero sintético profesional',
        'precio' => 39.99,
        'categoria' => 'boxeo',
        'stock' => 8,
        'imagen' => 'guantes.jpg'
    );
    
    $_SESSION['productos'][3] = array(
        'id' => 3,
        'nombre' => 'Cuerda para Saltar',
        'descripcion' => 'Cuerda ajustable con rodamientos',
        'precio' => 19.99,
        'categoria' => 'cardio',
        'stock' => 25,
        'imagen' => 'cuerda.jpg'
    );
    
    $_SESSION['productos'][4] = array(
        'id' => 4,
        'nombre' => 'Barra Olímpica',
        'descripcion' => 'Barra 20kg para levantamiento',
        'precio' => 129.99,
        'categoria' => 'pesas',
        'stock' => 5,
        'imagen' => 'barra.jpg'
    );
}

$categorias = array(
    'pesas' => '🏋️ Pesas',
    'boxeo' => '🥊 Boxeo',
    'cardio' => '🏃 Cardio',
    'yoga' => '🧘 Yoga'
);

if (!isset($_SESSION['usuarios'])) {
    $_SESSION['usuarios'] = array();
    
    $_SESSION['usuarios']['admin@deportes.com'] = array(
        'id' => 1,
        'nombre' => 'Administrador',
        'password' => 'admin123',
        'rol' => 'admin'
    );
    
    $_SESSION['usuarios']['cliente@ejemplo.com'] = array(
        'id' => 2,
        'nombre' => 'Juan Pérez',
        'password' => 'cliente123',
        'rol' => 'cliente'
    );
}

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = array();
}

$productos = &$_SESSION['productos'];
$usuarios = &$_SESSION['usuarios'];

$total_carrito = 0;
if (isset($_SESSION['carrito']) && is_array($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $cantidad) {
        $total_carrito += $cantidad;
    }
}

$usuario_logueado = false;
$usuario_nombre = '';
$usuario_rol = '';
$usuario_id = 0;
$usuario_email = '';

if (isset($_SESSION['usuario_id'])) {
    $usuario_logueado = true;
    $usuario_id = $_SESSION['usuario_id'];
    $usuario_nombre = $_SESSION['usuario_nombre'];
    $usuario_rol = $_SESSION['usuario_rol'];
    
    foreach ($usuarios as $email => $usuario) {
        if ($usuario['id'] == $usuario_id) {
            $usuario_email = $email;
            break;
        }
    }
}
?>