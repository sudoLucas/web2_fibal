<?php
include 'datos.php';

if (!$usuario_logueado) {
    header('Location: login.php');
    exit();
}

if (empty($_SESSION['carrito'])) {
    header('Location: carrito.php');
    exit(); 
}

$total = 0;
$lista_productos = "";
$items_carrito = array();

foreach ($_SESSION['carrito'] as $id_producto => $cantidad) {
    if (isset($productos[$id_producto])) {
        $producto = $productos[$id_producto];
        $subtotal = $producto['precio'] * $cantidad;
        $total += $subtotal;
        
        $items_carrito[] = array(
            'producto' => $producto,
            'cantidad' => $cantidad,
            'subtotal' => $subtotal
        );
        
        $lista_productos .= "• " . $producto['nombre'] . " (x" . $cantidad . ") - $" . number_format($subtotal, 2) . "%0A";
    }
}

$numero_whatsapp = "5491157416548"; 
$fecha = date('d/m/Y H:i:s');

$mensaje_whatsapp = "🚀 *NUEVO PEDIDO - SportShop*%0A";
$mensaje_whatsapp .= "------------------------%0A";
$mensaje_whatsapp .= "📅 Fecha: " . $fecha . "%0A";
$mensaje_whatsapp .= "👤 Cliente: " . $usuario_nombre . "%0A";
$mensaje_whatsapp .= "📧 Email: " . $usuario_email . "%0A";
$mensaje_whatsapp .= "------------------------%0A";
$mensaje_whatsapp .= "*PRODUCTOS:*%0A" . $lista_productos;
$mensaje_whatsapp .= "------------------------%0A";
$mensaje_whatsapp .= "💰 *TOTAL: $" . number_format($total, 2) . "*%0A";
$mensaje_whatsapp .= "🛒 Items: " . count($items_carrito) . "%0A";
$mensaje_whatsapp .= "------------------------%0A";
$mensaje_whatsapp .= "📍 *Pedido desde:* http://localhost/tebori/%0A";
$mensaje_whatsapp .= "ID Pedido: #" . rand(1000, 9999);

$url_whatsapp = "https://wa.me/" . $numero_whatsapp . "?text=" . $mensaje_whatsapp;

$id_pedido = rand(1000, 9999);
if (!isset($_SESSION['pedidos'])) {
    $_SESSION['pedidos'] = array();
}

$_SESSION['pedidos'][] = array(
    'id' => $id_pedido,
    'usuario_id' => $usuario_id,
    'fecha' => $fecha,
    'total' => $total,
    'items' => $items_carrito,
    'estado' => 'pendiente'
);

$_SESSION['carrito'] = array();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagar - SportShop</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        .confirmacion-pago {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin: 2rem auto;
            max-width: 600px;
        }
        
        .icono-exito {
            font-size: 4rem;
            color: #27ae60;
            margin-bottom: 1rem;
        }
        
        .detalles-pedido {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin: 2rem 0;
            text-align: left;
        }
        
        .item-pedido {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px dashed #ddd;
        }
        
        .whatsapp-btn {
            display: inline-block;
            background: #25D366;
            color: white;
            padding: 1rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-size: 1.2rem;
            margin: 1rem;
            transition: transform 0.3s;
        }
        
        .whatsapp-btn:hover {
            transform: scale(1.05);
            background: #128C7E;
        }
        
        .acciones-pago {
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <div class="encabezado">
        <h1>💳 Confirmar Pago</h1>
        <div class="menu">
            <a href="index.php">← Volver a tienda</a>
            <span>Cliente: <?php echo $usuario_nombre; ?></span>
        </div>
    </div>

    <div class="contenedor">
        <div class="confirmacion-pago">
            <div class="icono-exito">✅</div>
            <h1>¡Pedido Confirmado!</h1>
            <p>Tu pedido ha sido procesado exitosamente.</p>
            
            <div class="detalles-pedido">
                <h3>📋 Resumen del Pedido #<?php echo $id_pedido; ?></h3>
                
                <?php foreach ($items_carrito as $item): ?>
                <div class="item-pedido">
                    <div>
                        <strong><?php echo $item['producto']['nombre']; ?></strong><br>
                        <small>Cantidad: <?php echo $item['cantidad']; ?></small>
                    </div>
                    <div>$<?php echo number_format($item['subtotal'], 2); ?></div>
                </div>
                <?php endforeach; ?>
                
                <div class="item-pedido" style="border-top: 2px solid #333; font-weight: bold;">
                    <div>TOTAL:</div>
                    <div>$<?php echo number_format($total, 2); ?></div>
                </div>
            </div>
            
            <h3>📲 Enviar pedido por WhatsApp</h3>
            <p>Haz clic para enviar el resumen de tu pedido al administrador:</p>
            
            <a href="<?php echo $url_whatsapp; ?>" 
               target="_blank" 
               class="whatsapp-btn">
               📤 Enviar a WhatsApp
            </a>
            
            <div class="acciones-pago">
                <p><small>ID de pedido: <strong>#<?php echo $id_pedido; ?></strong></small></p>
                <p><small>Fecha: <?php echo $fecha; ?></small></p>
                
                <div style="margin-top: 2rem;">
                    <a href="index.php" class="btn">🏠 Seguir comprando</a>
                    <a href="mis_pedidos.php" class="btn">📋 Ver mis pedidos</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>