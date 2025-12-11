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
    if ($_GET['msg'] == 'add_ok') {
        $message = "¡Libro registrado exitosamente!";
    } elseif ($_GET['msg'] == 'edit_ok') {
        $message = "¡Libro modificado exitosamente!";
    } elseif ($_GET['msg'] == 'delete_ok') {
        $message = "¡Libro eliminado correctamente!";
    } elseif ($_GET['msg'] == 'stock_ok') {
        $message = "¡Inventario actualizado correctamente!";
    } elseif ($_GET['msg'] == 'error' && isset($_GET['detail'])) {
        $error_message = htmlspecialchars(urldecode($_GET['detail']));
    }
}

try {
    $sql = "SELECT 
                title_id,
                title,
                type,
                price,
                notes,
                pubdate
            FROM 
                titles
            ORDER BY
                title ASC";

    $stmt = $pdo->query($sql);
    $libros = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_message = "Error al cargar libros: " . $e->getMessage();
    $libros = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Libros</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        .action-btns a {
            padding: 5px 10px; margin-right: 5px; text-decoration: none;
            color: white; border-radius: 4px; font-size: 0.8em;
        }
        .success-message { background-color: #d4edda; color: #155724; padding: 10px; border: 1px solid #c3e6cb; border-radius: 5px; text-align: center; margin-bottom: 20px;}
        .error-message { background-color: #f8d7da; color: #721c24; padding: 10px; border: 1px solid #f5c6cb; border-radius: 5px; text-align: center; margin-bottom: 20px;}
        td.notas-col { font-size: 0.85em; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <h1>BiblioTec - Libros</h1>
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
                        <li><a href="#">Libros</a></li>
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
            <h2>Gestión de Libros</h2>
            
            <?php if ($message): ?>
                <div class="success-message"><?php echo $message; ?></div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <div style="margin-bottom: 15px;">
                <a href="insertar_libros.php" class="btn-dashboard btn-venta">Agregar Nuevo Libro</a>
            </div>
            
            <table class="history-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Tipo</th>
                        <th>Precio</th>
                        <th>Notas</th>
                        <th>Publicación</th>
                        <th>Acciones</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($libros) > 0): ?>
                        
                        <?php foreach ($libros as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['title_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo htmlspecialchars($row['type']); ?></td>
                                
                                <td>$<?php echo number_format($row['price'], 2); ?></td>
                                
                                <td class="notas-col" title="<?php echo htmlspecialchars($row['notes']); ?>">
                                    <?php echo htmlspecialchars($row['notes']); ?>
                                </td>
                                
                                <td><?php echo date('d/m/Y', strtotime($row['pubdate'])); ?></td>

                                <td class="action-btns" style="min-width: 150px;">
                                    <a href="stock.php?id=<?php echo $row['title_id']; ?>" class="btn-dashboard btn-secondary">Stock</a>
                                    <a href="editar_libro.php?id=<?php echo $row['title_id']; ?>" class="btn-dashboard btn-primary">Editar</a>
                                    
                                    <a href="eliminar_libro.php?id=<?php echo $row['title_id']; ?>" 
                                       class="btn-dashboard btn-productos" 
                                       onclick="return confirm('¿Estás seguro de eliminar el libro: <?php echo htmlspecialchars($row['title']); ?>?');">
                                       Eliminar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">No se encontraron libros registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>