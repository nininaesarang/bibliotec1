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
    if ($_GET['msg'] == 'add_ok') $message = "¡Autor registrado exitosamente!";
    elseif ($_GET['msg'] == 'edit_ok') $message = "¡Autor modificado exitosamente!";
    elseif ($_GET['msg'] == 'delete_ok') $message = "¡Autor eliminado correctamente!";
    elseif ($_GET['msg'] == 'biblio_ok') $message = "¡Bibliografía actualizada correctamente!";
    elseif ($_GET['msg'] == 'error' && isset($_GET['detail'])) {
        $error_message = htmlspecialchars(urldecode($_GET['detail']));
    }
}

try {
    $sql = "SELECT au_id, au_fname, au_lname, phone, address, city, state, zip, contract FROM authors ORDER BY au_lname ASC";
    $stmt = $pdo->query($sql);
    $autores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $autores = [];
    $error_message = "Error al cargar autores: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Autores</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        .action-btns a {
            padding: 5px 10px; margin-right: 5px; text-decoration: none;
            color: white; border-radius: 4px; font-size: 0.9em;
        }
        .success-message { background-color: #d4edda; color: #155724; padding: 10px; border: 1px solid #c3e6cb; border-radius: 5px; text-align: center; margin-bottom: 20px;}
        
        .history-table th, .history-table td { font-size: 0.9em; padding: 8px; }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <h1>BiblioTec - Autores</h1>
            <nav>
                <ul>
                    <li><a href="dashboard.php">Inicio</a></li>
                    
                    <?php if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] == 0): ?>
                        <li><a href="#" class="active">Autores</a></li>
                    <?php endif; ?>
                    
                    <li><a href="ventas.php">Nueva Venta</a></li>
                    
                    <?php if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] == 0): ?>
                        <li><a href="librerias.php">Librerías</a></li>
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
            <h2>Administración de Autores</h2>
            
            <?php if ($message): ?>
                <div class="success-message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <div style="margin-bottom: 15px;">
                <a href="insertar_autor.php" class="btn-dashboard btn-venta">Agregar Nuevo Autor</a>
            </div>
            
            <table class="history-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Teléfono</th>
                        <th>Dirección</th> 
                        <th>Ciudad</th> 
                        <th>Estado</th>
                        <th>C.P.</th>
                        <th>Contrato</th>
                        <th>Acciones</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($autores) > 0): ?>
                        
                        <?php foreach ($autores as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['au_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['au_fname']); ?></td>
                                <td><?php echo htmlspecialchars($row['au_lname']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                
                                <td><?php echo htmlspecialchars($row['address']); ?></td>
                                <td><?php echo htmlspecialchars($row['city']); ?></td>
                                <td><?php echo htmlspecialchars($row['state']); ?></td>
                                <td><?php echo htmlspecialchars($row['zip']); ?></td>
                                
                                <td><?php echo ($row['contract'] == 1) ? 'Sí' : 'No'; ?></td>

                                <td class="action-btns" style="min-width: 150px;">
                                    <a href="gestionar_libros_autor.php?id=<?php echo $row['au_id']; ?>" class="btn-dashboard btn-secondary">Obras</a>
                                    <a href="editar_autor.php?id=<?php echo $row['au_id']; ?>" class="btn-dashboard btn-primary">Editar</a>
                                    
                                    <a href="eliminar_autor.php?id=<?php echo $row['au_id']; ?>" 
                                       class="btn-dashboard btn-productos" 
                                       onclick="return confirm('¿Estás seguro de eliminar al autor <?php echo htmlspecialchars($row['au_fname']); ?>?');">
                                       Eliminar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="10" style="text-align: center;">No se encontraron autores registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>