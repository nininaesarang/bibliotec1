<?php
    $error = null;
    if (isset($_GET['error'])) {
        $error = "Usuario o contraseña incorrectos. Intenta de nuevo.";
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body{background-image: url('img/bibliotecas.jpg');
        background-size: cover;
        background-repeat: no-repeat;
        background-attachment: fixed;}
    </style>
    
</head>
<body>
    <div class="container">
        
        <div class="header">
            <h1>Iniciar sesión</h1>
            <p>Completa el formulario para iniciar sesión</p>
        </div>

        <form id="loginForm" class="form" action="validar_login.php" method="POST">
            <?php if ($error): ?>
                <p style="color: red; text-align: center;"><?php echo $error; ?></p>
            <?php endif; ?>
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input 
                    type="text" 
                    id="email" 
                    name="email" 
                    placeholder="ejemplo@correo.com"
                >
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Crea una contraseña segura"
                >
            </div>

            <button type="submit" class="btn-submit">Iniciar sesión</button>
        </form>
    </div>
</body>
</html>