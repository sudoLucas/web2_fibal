<?php
include 'datos.php';

if (!$usuario_logueado || $usuario_rol != 'admin') {
    header('Location: index.php');
    exit();
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    if ($accion == 'agregar') {
        $nombre = $_POST['nombre'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $precio = floatval($_POST['precio'] ?? 0);
        $categoria = $_POST['categoria'] ?? 'pesas';
        $stock = intval($_POST['stock'] ?? 0);
        
        if (!empty($nombre) && $precio > 0) {
            $nuevo_id = 1;
            if (!empty($_SESSION['productos'])) {
                $ids = array_keys($_SESSION['productos']);
                $nuevo_id = max($ids) + 1;
            }
            
            $_SESSION['productos'][$nuevo_id] = array(
                'id' => $nuevo_id,
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'precio' => $precio,
                'categoria' => $categoria,
                'stock' => $stock,
                'imagen' => 'default.jpg'
            );
            

            $productos = $_SESSION['productos'];
            
            $mensaje = "✅ Producto agregado exitosamente (ID: $nuevo_id)";
        } else {
            $mensaje = "❌ Error: Nombre y precio son obligatorios";
        }
    }

    if ($accion == 'eliminar') {
        $id_eliminar = intval($_POST['id_producto'] ?? 0);
        if ($id_eliminar > 0 && isset($_SESSION['productos'][$id_eliminar])) {
            unset($_SESSION['productos'][$id_eliminar]);
            $productos = $_SESSION['productos'];
            $mensaje = "✅ Producto eliminado";
        }
    }
    
    if ($accion == 'editar') {
        $id_editar = intval($_POST['id_producto'] ?? 0);
        if ($id_editar > 0 && isset($_SESSION['productos'][$id_editar])) {
            $_SESSION['productos'][$id_editar]['nombre'] = $_POST['nombre'] ?? $_SESSION['productos'][$id_editar]['nombre'];
            $_SESSION['productos'][$id_editar]['descripcion'] = $_POST['descripcion'] ?? $_SESSION['productos'][$id_editar]['descripcion'];
            $_SESSION['productos'][$id_editar]['precio'] = floatval($_POST['precio'] ?? $_SESSION['productos'][$id_editar]['precio']);
            $_SESSION['productos'][$id_editar]['categoria'] = $_POST['categoria'] ?? $_SESSION['productos'][$id_editar]['categoria'];
            $_SESSION['productos'][$id_editar]['stock'] = intval($_POST['stock'] ?? $_SESSION['productos'][$id_editar]['stock']);
            
            $productos = $_SESSION['productos'];
            $mensaje = "✅ Producto actualizado";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - SportShop</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <div class="encabezado">
        <h1>⚙️ Panel de Administración</h1>
        <div class="menu">
            <a href="index.php">← Volver a tienda</a>
            <span>Admin: <?php echo $usuario_nombre; ?></span>
            <a href="logout.php">Salir</a>
        </div>
    </div>

    <div class="contenedor">
        <?php if (!empty($mensaje)): ?>
            <div class="mensaje"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        
        <div class="admin-panel">
            <div class="seccion-admin">
                <h2>➕ Agregar Nuevo Producto</h2>
                <form method="POST" action="admin.php">
                    <input type="hidden" name="accion" value="agregar">
                    
                    <div class="grupo-form">
                        <label>Nombre:</label>
                        <input type="text" name="nombre" required>
                    </div>
                    
                    <div class="grupo-form">
                        <label>Descripción:</label>
                        <textarea name="descripcion" rows="3" required></textarea>
                    </div>
                    
                    <div class="grupo-form">
                        <label>Precio:</label>
                        <input type="number" name="precio" step="0.01" min="0" required>
                    </div>
                    
                    <div class="grupo-form">
                        <label>Categoría:</label>
                        <select name="categoria">
                            <?php foreach ($categorias as $clave => $nombre_cat): ?>
                                <option value="<?php echo $clave; ?>"><?php echo $nombre_cat; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="grupo-form">
                        <label>Stock:</label>
                        <input type="number" name="stock" min="0" required>
                    </div>
                    
                    <button type="submit" class="btn">Agregar Producto</button>
                </form>
            </div>
            
            <div class="seccion-admin">
                <h2>📋 Productos Existentes</h2>
                
                <?php if (empty($productos)): ?>
                    <p>No hay productos.</p>
                <?php else: ?>
                    <table class="tabla-admin">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productos as $producto): ?>
                            <tr>
                                <td><?php echo $producto['id']; ?></td>
                                <td><?php echo $producto['nombre']; ?></td>
                                <td>$<?php echo number_format($producto['precio'], 2); ?></td>
                                <td><?php echo $producto['stock']; ?></td>
                                <td>
                                    <form method="POST" action="admin.php" style="display: inline;">
                                        <input type="hidden" name="accion" value="editar">
                                        <input type="hidden" name="id_producto" value="<?php echo $producto['id']; ?>">
                                        <button type="submit" class="btn-chico" title="Editar">✏️</button>
                                    </form>
                                    
                                    <form method="POST" action="admin.php" style="display: inline;">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id_producto" value="<?php echo $producto['id']; ?>">
                                        <button type="submit" class="btn-chico btn-rojo" 
                                                onclick="return confirm('¿Eliminar este producto?')"
                                                title="Eliminar">
                                            🗑️
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            
                            <?php if (isset($_POST['accion']) && $_POST['accion'] == 'editar' && $_POST['id_producto'] == $producto['id']): ?>
                            <tr>
                                <td colspan="5">
                                    <form method="POST" action="admin.php" class="form-editar">
                                        <input type="hidden" name="accion" value="editar">
                                        <input type="hidden" name="id_producto" value="<?php echo $producto['id']; ?>">
                                        
                                        <div class="grupo-form">
                                            <label>Nombre:</label>
                                            <input type="text" name="nombre" value="<?php echo $producto['nombre']; ?>" required>
                                        </div>
                                        
                                        <div class="grupo-form">
                                            <label>Descripción:</label>
                                            <textarea name="descripcion" rows="2" required><?php echo $producto['descripcion']; ?></textarea>
                                        </div>
                                        
                                        <div class="grupo-form">
                                            <label>Precio:</label>
                                            <input type="number" name="precio" step="0.01" value="<?php echo $producto['precio']; ?>" required>
                                        </div>
                                        
                                        <div class="grupo-form">
                                            <label>Stock:</label>
                                            <input type="number" name="stock" value="<?php echo $producto['stock']; ?>" required>
                                        </div>
                                        
                                        <button type="submit" class="btn">Guardar Cambios</button>
                                        <a href="admin.php" class="btn btn-rojo">Cancelar</a>
                                    </form>
                                </td>
                            </tr>
                            <?php endif; ?>
                            
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>