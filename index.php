<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BiblioTec</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body{background-image: url('img/bibliotecas.jpg');
        background-size: cover;
        background-repeat: no-repeat;
        background-attachment: fixed;}
        a{color: white;
        text-decoration: none;
        font-weight: 600;
        }
        a:hover { text-decoration: underline; }
        .btn-submit{text-align: center;}
    </style>
</head>
<body>
    <div class="container">
        
        <div class="header">
            <h1>Bienvenido</h1>
            <p>Inicia sesión si eres usuario o revisa el stock si eres cliente</p>
        </div>

        <div class="btn-submit">
            <a href="login.php">Iniciar sesión</a>
        </div>
        <div class="btn-submit">
            <a href="cliente.php">Vista cliente</a>
        </div>
        
    </div>
</body>
</html>