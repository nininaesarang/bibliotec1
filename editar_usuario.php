<?php
session_start();
require 'conexion.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: usuarios.php");
    exit;
}

$id_usuario_editar = $_GET['id'];
$mensaje_error = "";

try {
    $sql = "SELECT nombre, email, rol FROM usuarios WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id_usuario_editar]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        header("Location: usuarios.php?msg=error&detail=UsuarioNoEncontrado");
        exit;
    }
} catch (PDOException $e) {
    die("Error al obtener usuario: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $rol = $_POST['rol'];
    $password_nueva = $_POST['password'];

    if (empty($nombre) || empty($email)) {
        $mensaje_error = "El nombre y el email no pueden estar vacíos.";
    } else {
        try {
            if (!empty($password_nueva)) {
                $pass_hash = password_hash($password_nueva, PASSWORD_DEFAULT);
                $sql_update = "UPDATE usuarios SET nombre = :nom, email = :ema, password = :pass, rol = :rol WHERE id = :id";
                $params = [
                    ':nom' => $nombre,
                    ':ema' => $email,
                    ':pass' => $pass_hash,
                    ':rol' => $rol,
                    ':id' => $id_usuario_editar
                ];
            } else {
                $sql_update = "UPDATE usuarios SET nombre = :nom, email = :ema, rol = :rol WHERE id = :id";
                $params = [
                    ':nom' => $nombre,
                    ':ema' => $email,
                    ':rol' => $rol,
                    ':id' => $id_usuario_editar
                ];
            }

            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->execute($params);

            header("Location: usuarios.php?msg=edit_ok");
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                $mensaje_error = "El correo electrónico ya está en uso por otro usuario.";
            } else {
                $mensaje_error = "Error al actualizar: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        .error-msg {
            background-color: #f8d7da; color: #721c24; padding: 10px;
            border-radius: 5px; margin-bottom: 15px; text-align: center;
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
                    <li class="user-welcome">Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?></li>
                    <li><a href="logout.php">Salir</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="form-container">
            <h2>Editar Usuario: <?php echo htmlspecialchars($usuario['nombre']); ?></h2>
            
            <?php if ($mensaje_error): ?>
                <div class="error-msg"><?php echo $mensaje_error; ?></div>
            <?php endif; ?>

            <form action="editar_usuario.php?id=<?php echo $id_usuario_editar; ?>" method="POST">
                
                <div class="form-group">
                    <label for="nombre">Nombre Completo</label>
                    <input type="text" id="nombre" name="nombre" required 
                           value="<?php echo htmlspecialchars($usuario['nombre']); ?>">
                </div>

                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo htmlspecialchars($usuario['email']); ?>">
                </div>

                <div class="form-group">
                    <label for="password">Contraseña (Dejar en blanco para no cambiar)</label>
                    <input type="password" id="password" name="password" 
                           placeholder="Solo escribe si quieres cambiarla">
                </div>

                <div class="form-group">
                    <label for="rol">Rol del Usuario</label>
                    <select id="rol" name="rol" required style="width: 100%; padding: 10px;">
                        <option value="1" <?php echo ($usuario['rol'] == 1) ? 'selected' : ''; ?>>Vendedor</option>
                        <option value="0" <?php echo ($usuario['rol'] == 0) ? 'selected' : ''; ?>>Administrador</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Guardar Cambios</button>
                    <a href="usuarios.php" class="btn-dashboard btn-historial" >Cancelar</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>