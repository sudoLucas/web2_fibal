<?php
include 'datos.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito - SportShop</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <div class="encabezado">
        <h1>🛒 Carrito de Compras</h1>
        <div class="menu">
            <a href="index.php">← Volver a productos</a>
            <?php if ($usuario_logueado): ?>
                <span>Hola, <?php echo $usuario_nombre; ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="contenedor">
        <?php
        $total_carrito = 0;
        $items_carrito = array();
        
        if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
            foreach ($_SESSION['carrito'] as $id_producto => $cantidad) {
                if (isset($productos[$id_producto])) {
                    $producto = $productos[$id_producto];
                    $subtotal = $producto['precio'] * $cantidad;
                    $total_carrito += $subtotal;
                    
                    $items_carrito[] = array(
                        'producto' => $producto,
                        'cantidad' => $cantidad,
                        'subtotal' => $subtotal
                    );
                }
            }
        }

        if (empty($items_carrito)) {
            echo '<div class="carrito-vacio">';
            echo '<h2>Tu carrito está vacío</h2>';
            echo '<p>Agrega productos desde nuestra tienda</p>';
            echo '<a href="index.php" class="btn">Ver Productos</a>';
            echo '</div>';
        } else {
        ?>
            <table class="tabla-carrito">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items_carrito as $item): ?>
                    <tr>
                        <td>
                            <strong><?php echo $item['producto']['nombre']; ?></strong><br>
                            <small><?php echo $item['producto']['descripcion']; ?></small>
                        </td>
                        <td>$<?php echo number_format($item['producto']['precio'], 2); ?></td>
                        <td><?php echo $item['cantidad']; ?></td>
                        <td>$<?php echo number_format($item['subtotal'], 2); ?></td>
                        <td>
                            <a href="quitar_carrito.php?id=<?php echo $item['producto']['id']; ?>" 
                               onclick="return confirm('¿Quitar este producto?')">
                                ❌ Quitar
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3"><strong>Total:</strong></td>
                        <td colspan="2"><strong>$<?php echo number_format($total_carrito, 2); ?></strong></td>
                    </tr>
                </tfoot>
            </table>
            
            <div class="acciones-carrito">
                <a href="index.php" class="btn">← Seguir comprando</a>
                <a href="vaciar_carrito.php" class="btn btn-rojo" 
                   onclick="return confirm('¿Vaciar todo el carrito?')">
                    🗑️ Vaciar Carrito
                </a>
                
                <?php if ($usuario_logueado): ?>
                    <a href="pagar.php" class="btn btn-verde">💳 Proceder al Pago</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-verde">🔐 Iniciar sesión para comprar</a>
                <?php endif; ?>
            </div>
        <?php } ?>
    </div>
</body>
</html>