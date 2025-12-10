<?php
session_start();

include 'datos.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $usuario_encontrado = false;
    
    foreach ($usuarios as $email_usuario => $datos_usuario) {
        if ($email == $email_usuario && $password == $datos_usuario['password']) {
            $_SESSION['usuario_id'] = $datos_usuario['id'];
            $_SESSION['usuario_nombre'] = $datos_usuario['nombre'];
            $_SESSION['usuario_rol'] = $datos_usuario['rol'];
            
            header('Location: index.php');
            exit();
        }
    }
    
    $error = 'Email o contraseña incorrectos';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SportShop</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body class="pagina-login">
    <div class="contenedor-login">
        <div class="login-box">
            <h1>🔐 Iniciar Sesión</h1>
            
            <?php if ($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="login.php">
                <div class="grupo-form">
                    <label>Email:</label>
                    <input type="email" name="email" required>
                </div>
                
                <div class="grupo-form">
                    <label>Contraseña:</label>
                    <input type="password" name="password" required>
                </div>
                
                <button type="submit" class="btn-login">Ingresar</button>
            </form>
            
            <div class="login-links">
                <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
                <p><a href="index.php">← Volver al inicio</a></p>
            </div>
            
            <div class="credenciales-prueba">
                <h3>Usuarios de prueba:</h3>
                <p><strong>Admin:</strong> admin@deportes.com / admin123</p>
                <p><strong>Cliente:</strong> cliente@ejemplo.com / cliente123</p>
            </div>
        </div>
    </div>
</body>
</html>