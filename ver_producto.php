<?php
include 'datos.php';

$id_producto = isset($_GET['id']) ? intval($_GET['id']) : 0;

$producto = null;
if (isset($productos[$id_producto])) {
    $producto = $productos[$id_producto];
}

if (!$producto) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $producto['nombre']; ?> - SportShop</title>
    <link rel="stylesheet" href="estilo.css"> 
</head>
<body>
    <div class="encabezado" style="text-align:center;">
        <h1 >Tebori Store</h1>
        <div class="menu">
            <a href="index.php">← Volver</a>
            <a href="carrito.php">🛒 Carrito (<?php echo $total_carrito; ?>)</a>
        </div>
    </div>

    <div class="contenedor">
        <div class="detalle-producto">
            <div class="detalle-imagen">
                <?php 
                if (isset($categorias[$producto['categoria']])) {
                    echo $categorias[$producto['categoria']];
                } else {
                    echo ' '; 
                }
                ?>
            </div>
            
            <div class="detalle-info">
                <h1><?php echo htmlspecialchars($producto['nombre']); ?></h1>
                <p class="descripcion"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                
                <div class="detalles">
                    <p><strong>Precio:</strong> $<?php echo number_format($producto['precio'], 2); ?></p>
                    <p><strong>Categoría:</strong> 
                        <?php 
                        if (isset($categorias[$producto['categoria']])) {
                            echo $categorias[$producto['categoria']];
                        } else {
                            echo $producto['categoria'];
                        }
                        ?>
                    </p>
                    <p><strong>Stock disponible:</strong> <?php echo $producto['stock']; ?> unidades</p>
                </div>
                
                <form action="agregar_carrito.php" method="POST" class="form-carrito">
                    <input type="hidden" name="id_producto" value="<?php echo $producto['id']; ?>">
                    
                    <div class="cantidad">
                        <label>Cantidad:</label>
                        <input type="number" name="cantidad" value="1" min="1" max="<?php echo $producto['stock']; ?>">
                    </div>
                    
                    <button type="submit" class="btn-agregar">Agregar al Carrito</button>
                </form>
                
                <a href="index.php" class="btn-volver">← Ver más productos</a>
            </div>
        </div>
    </div>
</body>
</html>
