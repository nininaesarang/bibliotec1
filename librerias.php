<?php
session_start();
require 'conexion.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$message = null;
$error_message = null;

if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'add_ok') $message = "¡Librería registrada exitosamente!";
    elseif ($_GET['msg'] == 'edit_ok') $message = "¡Librería modificada exitosamente!";
    elseif ($_GET['msg'] == 'delete_ok') $message = "¡Librería eliminada correctamente!";
    elseif ($_GET['msg'] == 'error' && isset($_GET['detail'])) {
        $error_message = htmlspecialchars(urldecode($_GET['detail']));
    }
}

try {
    $sql = "SELECT stor_id, stor_name, stor_address, city, state, zip 
            FROM stores 
            ORDER BY stor_name ASC";
            
    $stmt = $pdo->query($sql);
    $tiendas = $stmt->fetchAll(PDO::FETCH_ASSOC); 
} catch (PDOException $e) {
    $tiendas = [];
    $error_message = "Error al cargar las librerías: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Librerías</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        .action-btns a {
            padding: 5px 10px; margin-right: 5px; text-decoration: none;
            color: white; border-radius: 4px; font-size: 0.9em;
        }
        .btn-edit { background-color: #3498db; }
        .btn-delete { background-color: #e74c3c; }
        .success-message { background-color: #d4edda; color: #155724; padding: 10px; border: 1px solid #c3e6cb; border-radius: 5px; text-align: center; margin-bottom: 20px;}
        .error-message { background-color: #f8d7da; color: #721c24; padding: 10px; border: 1px solid #f5c6cb; border-radius: 5px; text-align: center; margin-bottom: 20px;}
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <h1>BiblioTec - Librerías</h1>
            <nav>
                <ul>
                    <li><a href="dashboard.php">Inicio</a></li>
                    <?php if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] == 0): ?>
                        <li><a href="autores.php">Autores</a></li>
                    <?php endif; ?>
                    <li><a href="ventas.php">Nueva Venta</a></li>
                    <?php if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] == 0): ?>
                        <li><a href="#">Librerías</a></li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] == 0): ?>
                        <li><a href="libros.php">Libros</a></li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] == 0): ?>
                        <li><a href="usuarios.php">Usuarios</a></li>
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
            <h2>Administración de Librerías</h2>
            
            <?php if ($message): ?>
                <div class="success-message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <div style="margin-bottom: 15px;">
                <a href="insertar_libreria.php" class="btn-dashboard btn-venta">Agregar Nueva Librería</a>
            </div>
            
            <table class="history-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Dirección</th>
                        <th>Ciudad / Edo</th>
                        <th>CP</th>
                        <th>Acciones</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($tiendas) > 0): ?>
                        
                        <?php foreach ($tiendas as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['stor_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['stor_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['stor_address']); ?></td>
                                
                                <td>
                                    <?php echo htmlspecialchars($row['city']); ?>, 
                                    <?php echo htmlspecialchars($row['state']); ?>
                                </td>
                                
                                <td><?php echo htmlspecialchars($row['zip']); ?></td>

                                <td class="action-btns" style="min-width: 150px;">
                                    <a href="editar_libreria.php?id=<?php echo $row['stor_id']; ?>" class="btn-dashboard btn-primary">Editar</a>
                                    
                                    <a href="eliminar_libreria.php?id=<?php echo $row['stor_id']; ?>" 
                                       class="btn-dashboard btn-productos" 
                                       onclick="return confirm('¿Estás seguro de eliminar la librería: <?php echo htmlspecialchars($row['stor_name']); ?>?');">
                                       Eliminar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No se encontraron tiendas registradas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>