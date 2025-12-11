<?php
session_start();
require 'conexion.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: libros.php");
    exit;
}

$id_libro = $_GET['id'];
$error_message = null;

try {
    $stmt = $pdo->prepare("SELECT * FROM titles WHERE title_id = :id");
    $stmt->execute([':id' => $id_libro]);
    $libro = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$libro) {
        header("Location: libros.php?msg=error&detail=LibroNoEncontrado");
        exit;
    }

    $stmt_pub = $pdo->query("SELECT pub_id, pub_name FROM publishers ORDER BY pub_name");
    $publishers = $stmt_pub->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al cargar datos: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $title = trim($_POST['title']);
    $type  = trim($_POST['type']);
    $price = $_POST['price']; 
    $notes = trim($_POST['notes']);
    $pub_id = $_POST['pub_id'];
    $pubdate = $_POST['pubdate'];

    if (empty($title) || empty($type)) {
        $error_message = "El Título y Tipo son obligatorios.";
    } else {
        try {
            $sql = "UPDATE titles 
                    SET title = :title, 
                        type = :type, 
                        pub_id = :pub_id, 
                        price = :price, 
                        notes = :notes, 
                        pubdate = :pubdate
                    WHERE title_id = :id";
            
            $stmt = $pdo->prepare($sql);
            
            $stmt->execute([
                ':title'   => $title,
                ':type'    => $type,
                ':pub_id'  => $pub_id,
                ':price'   => !empty($price) ? $price : null,
                ':notes'   => !empty($notes) ? $notes : null,
                ':pubdate' => !empty($pubdate) ? $pubdate : date('Y-m-d H:i:s'),
                ':id'      => $id_libro
            ]);

            header("Location: libros.php?msg=edit_ok");
            exit;

        } catch (PDOException $e) {
            $error_message = "Error al actualizar: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Libro</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        .error-msg {
            background-color: #f8d7da; color: #721c24; padding: 10px;
            border-radius: 5px; margin-bottom: 15px; text-align: center; border: 1px solid #f5c6cb;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%; padding: 8px; box-sizing: border-box;
            border: 1px solid #ccc; border-radius: 4px;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <h1>BiblioTec - Libros</h1>
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
            <h2>Editar Libro: <?php echo htmlspecialchars($libro['title_id']); ?></h2>
            
            <?php if ($error_message): ?>
                <div class="error-msg"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <form action="editar_libro.php?id=<?php echo $id_libro; ?>" method="POST">
                
                <div class="form-group">
                    <label for="title_id">Código del Libro (ID)</label>
                    <input type="text" id="title_id" name="title_id" 
                           value="<?php echo htmlspecialchars($libro['title_id']); ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="title">Título del Libro *</label>
                    <input type="text" id="title" name="title" required 
                           value="<?php echo htmlspecialchars($libro['title']); ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="type">Tipo / Categoría *</label>
                        <select id="type" name="type" required>
                            <option value="business" <?php echo ($libro['type'] == 'business') ? 'selected' : ''; ?>>Negocios</option>
                            <option value="psychology" <?php echo ($libro['type'] == 'psychology') ? 'selected' : ''; ?>>Psicología</option>
                            <option value="mod_cook" <?php echo ($libro['type'] == 'mod_cook') ? 'selected' : ''; ?>>Cocina Moderna</option>
                            <option value="popular_comp" <?php echo ($libro['type'] == 'popular_comp') ? 'selected' : ''; ?>>Computación Popular</option>
                            <option value="trad_cook" <?php echo ($libro['type'] == 'trad_cook') ? 'selected' : ''; ?>>Cocina Tradicional</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="pub_id">Editorial</label>
                        <select id="pub_id" name="pub_id">
                            <?php foreach ($publishers as $pub): ?>
                                <option value="<?php echo $pub['pub_id']; ?>" 
                                    <?php echo ($pub['pub_id'] == $libro['pub_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($pub['pub_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Precio ($)</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" 
                               value="<?php echo htmlspecialchars($libro['price']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="pubdate">Fecha de Publicación</label>
                        <input type="date" id="pubdate" name="pubdate" 
                               value="<?php echo date('Y-m-d', strtotime($libro['pubdate'])); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes">Resumen / Notas</label>
                    <textarea id="notes" name="notes" rows="3"><?php echo htmlspecialchars($libro['notes']); ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Guardar Cambios</button>
                    <a href="libros.php" class="btn-dashboard btn-historial">Cancelar</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>