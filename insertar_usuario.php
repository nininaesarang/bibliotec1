<?php
session_start();
require 'conexion.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error_message = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $rol = $_POST['rol'];
    $password_raw = $_POST['password'];

    if (empty($nombre) || empty($email) || empty($password_raw)) {
        $error_message = "Todos los campos son obligatorios.";
    } else {
        try {
            $password_hash = password_hash($password_raw, PASSWORD_DEFAULT);
            $sql = "INSERT INTO usuarios (nombre, email, rol, password) VALUES (:nom, :ema, :rol, :pass)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nom'  => $nombre,
                ':ema'  => $email,
                ':rol'  => $rol,
                ':pass' => $password_hash
            ]);

            header("Location: usuarios.php?msg=add_ok");
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                $error_message = "Error: El correo '$email' ya está registrado por otro usuario.";
            } else {
                $error_message = "Error de base de datos: " . $e->getMessage();
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
    <title>Registrar Usuario</title>
    <link rel="stylesheet" href="estilo.css"> <style>
        .error-msg {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }
        .form-group input[type="password"],
        .form-group input[type="email"],
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 15px;
        }

    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <h1>BiblioTec - Usuarios</h1>
            <nav>
                <ul>
                    <li><a href="dashboard.php">Inicio</a></li>
                    <li><a href="autores.php">Autores</a></li>
                    <li><a href="ventas.php">Nueva Venta</a></li>
                    <li><a href="librerias.php">Librerías</a></li>
                    <li><a href="libros.php">Libros</a></li>
                    <li><a href="usuarios.php">Usuarios</a></li>
                    <li class="user-welcome">
                        Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                    </li>
                    <li><a href="logout.php">Salir</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="form-container">
            <h2>Registrar Nuevo Usuario</h2>
            
            <?php if ($error_message): ?>
                <div class="error-msg"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <form id="productForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                
                <div class="form-group">
                    <label for="nombre">Nombre Completo</label>
                    <input type="text" id="nombre" name="nombre" required placeholder="Ej: Juan Pérez">
                </div>
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" required placeholder="usuario@bibliotec.com">
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required placeholder="Asigna una contraseña">
                </div>
                <div class="form-row">
                    <div>
                        <label for="rol">Rol del Usuario</label>
                        <select id="rol" name="rol" required style="width: 100%; padding: 10px;">
                            <option value="1">Vendedor</option>
                            <option value="0">Administrador</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Registrar Usuario</button>
                    <a href="usuarios.php" class="btn-dashboard btn-historial">Cancelar</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>