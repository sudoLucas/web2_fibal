<?php
include 'datos.php';

$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmar_password = $_POST['confirmar_password'] ?? '';
    
    if (empty($nombre) || empty($email) || empty($password)) {
        $error = 'Todos los campos son obligatorios';
    } elseif ($password != $confirmar_password) {
        $error = 'Las contraseñas no coinciden';
    } elseif (strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres';
    } else {
        $query_check = "SELECT id FROM usuarios WHERE email = :email";
        $stmt_check = $conn->prepare($query_check);
        $stmt_check->bindParam(':email', $email);
        $stmt_check->execute();
        
        if ($stmt_check->rowCount() > 0) {
            $error = 'Este email ya está registrado';
        } else {
            $query = "INSERT INTO usuarios (nombre, email, password, rol) 
                     VALUES (:nombre, :email, :password, 'cliente')";
            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password); // En producción usar password_hash()
            
            if ($stmt->execute()) {
                $nuevo_id = $conn->lastInsertId();
                
                $_SESSION['usuario_id'] = $nuevo_id;
                $_SESSION['usuario_nombre'] = $nombre;
                $_SESSION['usuario_rol'] = 'cliente';
                
                header('Location: index.php');
                exit();
            } else {
                $error = 'Error al registrar usuario';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Tebori</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body class="pagina-login">
    <div class="contenedor-login">
        <div class="login-box">
            <h1>Crear Cuenta - Tebori</h1>
            
            <?php if ($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="registro.php">
                <div class="grupo-form">
                    <label>Nombre completo:</label>
                    <input type="text" name="nombre" required>
                </div>
                
                <div class="grupo-form">
                    <label>Email:</label>
                    <input type="email" name="email" required>
                </div>
                
                <div class="grupo-form">
                    <label>Contraseña:</label>
                    <input type="password" name="password" required minlength="6">
                </div>
                
                <div class="grupo-form">
                    <label>Confirmar contraseña:</label>
                    <input type="password" name="confirmar_password" required>
                </div>
                
                <button type="submit" class="btn-login">Registrarse</button>
            </form>
            
            <div class="login-links">
                <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
                <p><a href="index.php">← Volver al inicio</a></p>
            </div>
        </div>
    </div>
    
</body>
</html>
