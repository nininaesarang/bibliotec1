<?php
session_start();
include 'conexion.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$message = null;
$error_message = null;

if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'add_ok') {
        $message = "¡Usuario registrado exitosamente!";
    } elseif ($_GET['msg'] == 'edit_ok') {
        $message = "¡Usuario modificado exitosamente!";
    } elseif ($_GET['msg'] == 'delete_ok') {
        $message = "¡Usuario eliminado correctamente!";
    } elseif ($_GET['msg'] == 'error' && isset($_GET['detail'])) { // <-- MANEJO DEL ERROR
        $error_message = htmlspecialchars(urldecode($_GET['detail']));
    }
}

$sql = "SELECT id, nombre, email, rol, fecha_registro, CASE rol
        WHEN 1 THEN 'Vendedor'
        WHEN 0 THEN 'Administrador'
        ELSE 'Desconocido'
    END AS tipo_de_usuario  FROM usuarios";
$stmt = $pdo->query($sql);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Usuarios</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        .action-btns a {
            padding: 5px 10px;
            margin-right: 5px;
            text-decoration: none;
            color: white;
            border-radius: 4px;
            font-size: 0.9em;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            text-align: center;
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
                    <?php if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] == 0): ?>
                        <li><a href="autores.php">Autores</a></li>
                    <?php endif; ?>
                    <li><a href="ventas.php">Nueva Venta</a></li>
                    <?php if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] == 0): ?>
                        <li><a href="librerias.php">Librerías</a></li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] == 0): ?>
                        <li><a href="libros.php">Libros</a></li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] == 0): ?>
                        <li><a href="#">Usuarios</a></li>
                    <?php endif; ?>
                    <li class="user-welcome">
                        Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                    </li>
                    <li><a href="logout.php">Salir</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="table-container">
            <h2>Administración de Usuarios</h2>
            
            <?php if ($message): ?>
                <div class="success-message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <div style="margin-bottom: 15px;">
                <a href="insertar_usuario.php" class="btn-dashboard btn-venta">Agregar Nuevo Usuario</a>
            </div>
            
            <table class="history-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Tipo de usuario</th>
                        <th>Fecha de Registro</th>
                        <th>Acciones</th> </tr>
                </thead>
                <tbody>
    <?php if (count($usuarios) > 0): ?>
        
        <?php foreach ($usuarios as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                
                <td><?php echo htmlspecialchars($row['tipo_de_usuario']); ?></td>
                
                <td><?php echo htmlspecialchars($row['fecha_registro']); ?></td>

                <td class="action-btns">
                    <a href="editar_usuario.php?id=<?php echo $row['id']; ?>" class="btn-dashboard btn-primary">Editar</a>
                    
                    <a href="eliminar_usuario.php?id=<?php echo $row['id']; ?>" 
                       class="btn-dashboard btn-productos" 
                       onclick="return confirm('¿Estás seguro de eliminar a <?php echo htmlspecialchars($row['nombre']); ?>?');">
                       Eliminar
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>

    <?php else: ?>
        <tr>
            <td colspan="6" style="text-align: center;">No se encontraron usuarios registrados.</td>
        </tr>
    <?php endif; ?>
</tbody>
            </table>
        </div>
    </main>
</body>
</html>