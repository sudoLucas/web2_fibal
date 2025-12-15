<?php
include 'datos.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Buscar usuario en base de datos
    $query = "SELECT * FROM usuarios WHERE email = :email";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verificar contraseña (en producción usar password_verify())
        if ($password == $usuario['password']) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_rol'] = $usuario['rol'];
            
            header('Location: index.php');
            exit();
        } else {
            $error = 'Contraseña incorrecta';
        }
    } else {
        $error = 'Email no registrado';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tebori</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        .credenciales-prueba {
            margin-top: 30px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #3498db;
        }
        
        .credenciales-prueba h3 {
            margin-top: 0;
            color: #2c3e50;
            font-size: 1rem;
            border-bottom: 1px dashed #ddd;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        
        .credencial-item {
            margin: 10px 0;
            padding: 10px;
            background: white;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .email {
            color: #2980b9;
            font-weight: bold;
            font-family: monospace;
        }
        
        .password {
            color: #e74c3c;
            font-family: monospace;
        }
        
        .btn-pequeno {
            background: #3498db;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            transition: background 0.3s;
            white-space: nowrap;
        }
        
        .btn-pequeno:hover {
            background: #2980b9;
        }
        
        .credencial-info {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
    </style>
</head>
<body class="pagina-login">
    <div class="contenedor-login">
        <div class="login-box">
            <h1>Iniciar Sesión - Tebori</h1>
            
            <?php if ($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="login.php">
                <div class="grupo-form">
                    <label>Email:</label>
                    <input type="email" name="email" required id="email-input">
                </div>
                
                <div class="grupo-form">
                    <label>Contraseña:</label>
                    <input type="password" name="password" required id="password-input">
                </div>
                
                <button type="submit" class="btn-login">Ingresar</button>
            </form>
            
            <div class="login-links">
                <p>¿No tenés cuenta? <a href="registro.php">Registrate aquí</a></p>
                <p><a href="index.php">← Volver al inicio</a></p>
            </div>
            
            <div class="credenciales-prueba">
                <h3>Usuarios de prueba (click para autocompletar):</h3>
                <div class="credencial-item">
                    <div class="credencial-info">
                        <strong>Admin:</strong> 
                        <span class="email">admin@tebori.com</span>
                        <span class="password">admin123</span>
                    </div>
                    <button onclick="rellenarFormulario('admin@tebori.com', 'admin123')" class="btn-pequeno">
                        Usar este usuario
                    </button>
                </div>
                <div class="credencial-item">
                    <div class="credencial-info">
                        <strong>Cliente:</strong> 
                        <span class="email">cliente@tebori.com</span>
                        <span class="password">cliente123</span>
                    </div>
                    <button onclick="rellenarFormulario('cliente@tebori.com', 'cliente123')" class="btn-pequeno">
                        Usar este usuario
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    function rellenarFormulario(email, password) {
        document.getElementById('email-input').value = email;
        document.getElementById('password-input').value = password;
        
        const emailInput = document.getElementById('email-input');
        const originalBorder = emailInput.style.border;
        const originalBackground = emailInput.style.backgroundColor;
        
        emailInput.style.border = '2px solid #27ae60';
        emailInput.style.backgroundColor = '#f0fff4';
        
        setTimeout(() => {
            emailInput.style.border = originalBorder;
            emailInput.style.backgroundColor = originalBackground;
        }, 1000);
    }
    </script>
</body>
</html>
