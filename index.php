<?php
include 'datos.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportShop - Inicio</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <div class="encabezado">
        <h1>🏋️ SportShop</h1>
        <p>Tienda de Equipamiento Deportivo</p>
        
        <div class="menu">
            <a href="index.php">Inicio</a>
            <a href="carrito.php">🛒 Carrito (<?php echo $total_carrito; ?>)</a>
            
            <?php if ($usuario_logueado): ?>
                <span>Hola, <?php echo $usuario_nombre; ?></span>
                <?php if ($usuario_rol == 'admin'): ?>
                    <a href="../admin/admin.php">Admin</a>
                <?php endif; ?>
                <a href="../includes/logout.php">Salir</a>
            <?php else: ?>
                <a href="login.php">Ingresar</a>
                <a href="registro.php">Registrarse</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="categorias">
        <a href="index.php">Todos</a>
        <?php foreach ($categorias as $clave => $nombre): ?>
            <a href="index.php?categoria=<?php echo $clave; ?>">
                <?php echo $nombre; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="contenedor">
        <h2>Productos Disponibles (<?php echo count($productos); ?> productos)</h2>
        
        <?php if (empty($productos)): ?>
            <div class="aviso">
                <p>No hay productos disponibles.</p>
                <?php if ($usuario_rol == 'admin'): ?>
                    <a href="admin.php">Agregar productos desde el panel admin</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="productos">
                <?php
                $productos_a_mostrar = $productos;
                if (isset($_GET['categoria']) && !empty($_GET['categoria'])) {
                    $categoria_filtro = $_GET['categoria'];
                    $productos_a_mostrar = array();
                    
                    foreach ($productos as $producto) {
                        if ($producto['categoria'] == $categoria_filtro) {
                            $productos_a_mostrar[] = $producto;
                        }
                    }
                }
                
                foreach ($productos_a_mostrar as $producto):
                ?>
                    <div class="producto">
                        <div class="producto-imagen">
                            <?php echo $categorias[$producto['categoria']]; ?>
                        </div>
                        <div class="producto-info">
                            <h3><?php echo $producto['nombre']; ?></h3>
                            <p><?php echo $producto['descripcion']; ?></p>
                            <p class="precio">$<?php echo number_format($producto['precio'], 2); ?></p>
                            <p class="stock">Stock: <?php echo $producto['stock']; ?> unidades</p>
                            
                            <?php if ($producto['stock'] > 0): ?>
                                <form action="agregar_carrito.php" method="POST">
                                    <input type="hidden" name="id_producto" value="<?php echo $producto['id']; ?>">
                                    <input type="number" name="cantidad" value="1" min="1" max="<?php echo $producto['stock']; ?>" style="width: 60px;">
                                    <button type="submit">🛒 Agregar</button>
                                </form>
                            <?php else: ?>
                                <p class="sin-stock">❌ Sin stock</p>
                            <?php endif; ?>
                            
                            <a href="ver_producto.php?id=<?php echo $producto['id']; ?>" class="ver-mas">
                                Ver detalles
                            </a>
                            
                            <?php if ($usuario_rol == 'admin'): ?>
                                <div class="admin-actions">
                                    <small>
                                        <a href="admin.php?editar=<?php echo $producto['id']; ?>" style="color: #3498db;">✏️ Editar</a> | 
                                        <a href="admin.php?eliminar=<?php echo $producto['id']; ?>" 
                                           onclick="return confirm('¿Eliminar este producto?')"
                                           style="color: #e74c3c;">🗑️ Eliminar</a>
                                    </small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="pie">
        <p>&copy; <?php echo date('Y'); ?> SportShop - Todos los derechos reservados</p>
        <small>Productos en sistema: <?php echo count($productos); ?> | Usuarios registrados: <?php echo count($usuarios); ?></small>
    </div>
</body>
</html>