<?php
include 'datos.php';

if (!$usuario_logueado || $usuario_rol != 'admin') {
    header('Location: index.php');
    exit();
}

// Configuración para subir imágenes
$directorio_uploads = 'imgs/';
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
            // Manejar subida de imagen
            $nombre_imagen = 'default.jpg';
            
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
                $archivo_temporal = $_FILES['imagen']['tmp_name'];
                $nombre_original = $_FILES['imagen']['name'];
                $extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));
                
                // Extensiones permitidas
                $extensiones_permitidas = array('jpg', 'jpeg', 'png', 'gif', 'webp');
                
                if (in_array($extension, $extensiones_permitidas)) {
                    // Generar nombre único
                    $nombre_imagen = uniqid() . '_' . time() . '.' . $extension;
                    $ruta_destino = $directorio_uploads . $nombre_imagen;
                    
                    // Mover archivo
                    if (move_uploaded_file($archivo_temporal, $ruta_destino)) {
                        // Redimensionar si es muy grande (opcional)
                        // Aquí podrías agregar código para redimensionar
                    } else {
                        $nombre_imagen = 'default.jpg';
                        $mensaje = " Producto agregado, pero hubo un error al subir imagen";
                    }
                } else {
                    $nombre_imagen = 'default.jpg';
                    $mensaje = " Producto agregado, pero  el formato de imagen no fue valido";
                }
            }
            
            // Insertar en base de datos
            $query = "INSERT INTO productos (nombre, descripcion, precio, categoria, stock, imagen) 
                     VALUES (:nombre, :descripcion, :precio, :categoria, :stock, :imagen)";
            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':precio', $precio);
            $stmt->bindParam(':categoria', $categoria);
            $stmt->bindParam(':stock', $stock);
            $stmt->bindParam(':imagen', $nombre_imagen);
            
            if ($stmt->execute()) {
                $nuevo_id = $conn->lastInsertId();
                $mensaje = " Producto agregado exitosamente (ID: $nuevo_id)";
                
                // Actualizar array de productos
                $productos = obtenerProductos($conn);
            } else {
                $mensaje = " Error al agregar producto";
            }
        } else {
            $mensaje = "Error: Nombre y precio deben ser obligatorios";
        }
    }
    
    if ($accion == 'eliminar') {
        $id_eliminar = intval($_POST['id_producto'] ?? 0);
        
        if ($id_eliminar > 0) {
            $query_img = "SELECT imagen FROM productos WHERE id = :id";
            $stmt_img = $conn->prepare($query_img);
            $stmt_img->bindParam(':id', $id_eliminar);
            $stmt_img->execute();
            $producto_img = $stmt_img->fetch(PDO::FETCH_ASSOC);
            
            $query = "UPDATE productos SET activo = 0 WHERE id = :id";
            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $id_eliminar);
            
            if ($stmt->execute()) {
                if ($producto_img && $producto_img['imagen'] != 'default.jpg') {
                    $ruta_imagen = $directorio_uploads . $producto_img['imagen'];
                    if (file_exists($ruta_imagen)) {
                        unlink($ruta_imagen);
                    }
                }
                $mensaje = "Producto eliminado exitosamente";
                $productos = obtenerProductos($conn);
            }
        }
    }
    
    if ($accion == 'editar') {
        $id_editar = intval($_POST['id_producto'] ?? 0);
        
        if ($id_editar > 0) {
            $query_select = "SELECT * FROM productos WHERE id = :id";
            $stmt_select = $conn->prepare($query_select);
            $stmt_select->bindParam(':id', $id_editar);
            $stmt_select->execute();
            $producto_actual = $stmt_select->fetch(PDO::FETCH_ASSOC);
            
            if ($producto_actual) {
                $nombre = $_POST['nombre'] ?? $producto_actual['nombre'];
                $descripcion = $_POST['descripcion'] ?? $producto_actual['descripcion'];
                $precio = floatval($_POST['precio'] ?? $producto_actual['precio']);
                $categoria = $_POST['categoria'] ?? $producto_actual['categoria'];
                $stock = intval($_POST['stock'] ?? $producto_actual['stock']);
                $imagen = $producto_actual['imagen'];
                
                if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
                    $archivo_temporal = $_FILES['imagen']['tmp_name'];
                    $nombre_original = $_FILES['imagen']['name'];
                    $extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));
                    $extensiones_permitidas = array('jpg', 'jpeg', 'png', 'gif', 'webp');
                    
                    if (in_array($extension, $extensiones_permitidas)) {
                        $nueva_imagen = uniqid() . '_' . time() . '.' . $extension;
                        $ruta_destino = $directorio_uploads . $nueva_imagen;
                        
                        if (move_uploaded_file($archivo_temporal, $ruta_destino)) {
                            if ($imagen != 'default.jpg') {
                                $ruta_anterior = $directorio_uploads . $imagen;
                                if (file_exists($ruta_anterior)) {
                                    unlink($ruta_anterior);
                                }
                            }
                            $imagen = $nueva_imagen;
                        }
                    }
                }
                
                $query = "UPDATE productos SET nombre = :nombre, descripcion = :descripcion, 
                         precio = :precio, categoria = :categoria, stock = :stock, imagen = :imagen 
                         WHERE id = :id";
                
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':descripcion', $descripcion);
                $stmt->bindParam(':precio', $precio);
                $stmt->bindParam(':categoria', $categoria);
                $stmt->bindParam(':stock', $stock);
                $stmt->bindParam(':imagen', $imagen);
                $stmt->bindParam(':id', $id_editar);
                
                if ($stmt->execute()) {
                    $mensaje = "Producto actualizado exitosamente";
                    $productos = obtenerProductos($conn);
                }
            }
        }
    }
}

$productos = obtenerProductos($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Tebori</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        .imagen-preview {
            max-width: 100px;
            max-height: 100px;
            margin: 10px 0;
        }
        .form-editar img {
            max-width: 150px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="encabezado">
        <h1>🔧 Panel de Administración - Tebori</h1>
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
                <h2>Agregar Nuevo Producto</h2>
                <form method="POST" action="admin.php" enctype="multipart/form-data">
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
                    
                    <div class="grupo-form">
                        <label>Imagen:</label>
                        <input type="file" name="imagen" accept="image/*">
                        <small>Formatos: JPG, PNG, GIF, WEBP. Máx 2MB</small>
                    </div>
                    
                    <button type="submit" class="btn">Agregar Producto</button>
                </form>
            </div>
            
            <div class="seccion-admin">
                <h2>Productos Existentes (<?php echo count($productos); ?>)</h2>
                
                <?php if (empty($productos)): ?>
                    <p>No hay productos.</p>
                <?php else: ?>
                    <table class="tabla-admin">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Imagen</th>
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
                                <td>
                                    <?php if ($producto['imagen'] && file_exists($directorio_uploads . $producto['imagen'])): ?>
                                        <img src="<?php echo $directorio_uploads . $producto['imagen']; ?>" 
                                             alt="<?php echo $producto['nombre']; ?>" 
                                             class="imagen-preview">
                                    <?php else: ?>
                                        <div style="width: 50px; height: 50px; background: #eee; display: flex; align-items: center; justify-content: center;">
                                            📷
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $producto['nombre']; ?></td>
                                <td>$<?php echo number_format($producto['precio'], 2); ?></td>
                                <td><?php echo $producto['stock']; ?></td>
                                <td>
                                    <form method="POST" action="admin.php" style="display: inline;">
                                        <input type="hidden" name="accion" value="editar">
                                        <input type="hidden" name="id_producto" value="<?php echo $producto['id']; ?>">
                                        <button type="submit" class="btn-chico" title="Editar">Editar</button>
                                    </form>
                                    
                                    <form method="POST" action="admin.php" style="display: inline;">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id_producto" value="<?php echo $producto['id']; ?>">
                                        <button type="submit" class="btn-chico btn-rojo" 
                                                onclick="return confirm('¿querés eliminar este producto?')"
                                                title="Eliminar">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            
                            <?php if (isset($_POST['accion']) && $_POST['accion'] == 'editar' && $_POST['id_producto'] == $producto['id']): ?>
                            <tr>
                                <td colspan="6">
                                    <form method="POST" action="admin.php" class="form-editar" enctype="multipart/form-data">
                                        <input type="hidden" name="accion" value="editar">
                                        <input type="hidden" name="id_producto" value="<?php echo $producto['id']; ?>">
                                        
                                        <h3>Editando: <?php echo $producto['nombre']; ?></h3>
                                        
                                        <?php if ($producto['imagen'] && file_exists($directorio_uploads . $producto['imagen'])): ?>
                                            <img src="<?php echo $directorio_uploads . $producto['imagen']; ?>" 
                                                 alt="Imagen actual" style="max-width: 200px;">
                                            <br>
                                        <?php endif; ?>
                                        
                                        <div class="grupo-form">
                                            <label>Nueva imagen (opcional):</label>
                                            <input type="file" name="imagen" accept="image/*">
                                            <small>Dejar vacío para mantener la imagen actual</small>
                                        </div>
                                        
                                        <div class="grupo-form">
                                            <label>Nombre:</label>
                                            <input type="text" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
                                        </div>
                                        
                                        <div class="grupo-form">
                                            <label>Descripción:</label>
                                            <textarea name="descripcion" rows="2" required><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
                                        </div>
                                        
                                        <div class="grupo-form">
                                            <label>Precio:</label>
                                            <input type="number" name="precio" step="0.01" value="<?php echo $producto['precio']; ?>" required>
                                        </div>
                                        
                                        <div class="grupo-form">
                                            <label>Categoría:</label>
                                            <select name="categoria">
                                                <?php foreach ($categorias as $clave => $nombre_cat): ?>
                                                    <option value="<?php echo $clave; ?>" 
                                                        <?php echo ($producto['categoria'] == $clave) ? 'selected' : ''; ?>>
                                                        <?php echo $nombre_cat; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
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
