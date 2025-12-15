<?php
include 'datos.php';

$categoria_filtro = $_GET['categoria'] ?? null;
$productos_array = obtenerProductos($conn, $categoria_filtro);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tebori - Tienda Deportiva</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <div class="encabezado" style="text-align:center;">
        <h1 >Tebori - Tienda Deportiva</h1>
        <p>Equipamiento profesional para tu entrenamiento</p>
        
        <div class="menu">
            <a href="index.php">Inicio</a>
            <a href="carrito.php">Carrito (<?php echo $total_carrito; ?>)</a>
            
            <?php if ($usuario_logueado): ?>
                <span>Hola, <?php echo $usuario_nombre; ?></span>
                <?php if ($usuario_rol == 'admin'): ?>
                    <a href="admin.php">Admin</a>
                <?php endif; ?>
                <a href="logout.php">Salir</a>
            <?php else: ?>
                <a href="login.php">Ingresar</a>
                <a href="registro.php">Registrarse</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="categorias">
        <a href="index.php">Todos</a>
        <?php foreach ($categorias as $clave => $nombre): ?>
            <a href="index.php?categoria=<?php echo $clave; ?>" 
               class="<?php echo ($categoria_filtro == $clave) ? 'activa' : ''; ?>">
                <?php echo $nombre; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="contenedor">
        <h2>Productos Disponibles (<?php echo count($productos_array); ?> productos)</h2>
        
        <?php if (empty($productos_array)): ?>
            <div class="aviso">
                <p>No hay productos disponibles en esta categoría.</p>
                <?php if ($usuario_rol == 'admin'): ?>
                    <a href="admin.php">Agregar productos desde el panel admin</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="productos">
                <?php foreach ($productos_array as $producto): ?>
                    <div class="producto">
                        <div class="producto-imagen">
                            <?php 
                            if ($producto['imagen'] && file_exists('uploads/' . $producto['imagen'])): 
                            ?>
                                <img src="uploads/<?php echo $producto['imagen']; ?>" 
                                     alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                            <?php else: ?>
                                <div class="icono-categoria">
                                    <?php echo $categorias[$producto['categoria']] ?? '🎯'; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="producto-info">
                            <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                            <p class="descripcion"><?php echo htmlspecialchars(substr($producto['descripcion'], 0, 100)) . '...'; ?></p>
                            <p class="precio">$<?php echo number_format($producto['precio'], 2); ?></p>
                            <p class="stock <?php echo ($producto['stock'] > 0) ? 'con-stock' : 'sin-stock'; ?>">
                                Stock: <?php echo $producto['stock']; ?> unidades
                            </p>
                            
                            <?php if ($producto['stock'] > 0): ?>
                                <form action="agregar_carrito.php" method="POST">
                                    <input type="hidden" name="id_producto" value="<?php echo $producto['id']; ?>">
                                    <div class="form-carrito">
                                        <input type="number" name="cantidad" value="1" min="1" max="<?php echo $producto['stock']; ?>">
                                        <button type="submit">Agregar</button>
                                    </div>
                                </form>
                            <?php else: ?>
                                <p class="sin-stock">Sin stock</p>
                            <?php endif; ?>
                            
                            <a href="ver_producto.php?id=<?php echo $producto['id']; ?>" class="ver-mas">
                                Ver detalles
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="pie">
        <p>&copy; <?php echo date('Y'); ?> Tebori - Tienda Deportiva. Todos los derechos reservados.</p>
    </div>
</body>
</html>
