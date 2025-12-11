<?php
// crear_libro.php

// 1. INICIAR SESIÓN Y CONEXIÓN
session_start();
require 'conexion.php'; 

// 2. SEGURIDAD
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// 3. OBTENER EDITORIALES (Para el formulario)
try {
    $stmt_pub = $pdo->query("SELECT pub_id, pub_name FROM publishers ORDER BY pub_name");
    $publishers = $stmt_pub->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $publishers = [];
}

$error_message = null;

// 4. PROCESAR FORMULARIO
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id      = trim($_POST['title_id']);
    $title   = trim($_POST['title']);
    $type    = trim($_POST['type']);
    $price   = $_POST['price'];
    $notes   = trim($_POST['notes']);
    $pub_id  = $_POST['pub_id'];
    $pubdate = $_POST['pubdate'];

    if (empty($id) || empty($title) || empty($type)) {
        $error_message = "El ID, Título y Tipo son obligatorios.";
    } else {
        try {
            // INICIAMOS UNA TRANSACCIÓN
            // Esto asegura que se inserten el libro Y el stock, o ninguno de los dos.
            $pdo->beginTransaction();

            // PASO A: Insertar el Libro en 'titles'
            $sql = "INSERT INTO titles (title_id, title, type, pub_id, price, notes, pubdate) 
                    VALUES (:id, :title, :type, :pub_id, :price, :notes, :pubdate)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id'      => $id,
                ':title'   => $title,
                ':type'    => $type,
                ':pub_id'  => $pub_id,
                ':price'   => !empty($price) ? $price : null,
                ':notes'   => !empty($notes) ? $notes : null,
                ':pubdate' => !empty($pubdate) ? $pubdate : date('Y-m-d H:i:s')
            ]);

            // PASO B: Registrar el libro en 'stockInStores' (Inicialización)
            
            // 1. Obtenemos todas las tiendas existentes
            $stmt_stores = $pdo->query("SELECT stor_id FROM stores");
            $tiendas = $stmt_stores->fetchAll(PDO::FETCH_COLUMN); // Trae solo los IDs en un array simple

            // 2. Preparamos la inserción de stock
            $sql_stock = "INSERT INTO stockInStores (stor_id, title_id, qty) VALUES (?, ?, ?)";
            $stmt_stock = $pdo->prepare($sql_stock);

            // 3. Recorremos cada tienda e insertamos el libro con Cantidad 0
            foreach ($tiendas as $tienda_id) {
                // Insertamos: ID Tienda, ID Libro Nuevo, Cantidad 0
                $stmt_stock->execute([$tienda_id, $id, 0]);
            }

            // SI TODO SALIÓ BIEN, GUARDAMOS LOS CAMBIOS
            $pdo->commit();

            header("Location: libros.php?msg=add_ok");
            exit;

        } catch (PDOException $e) {
            // SI HUBO ERROR, DESHACEMOS CUALQUIER CAMBIO (Rollback)
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            if ($e->getCode() == '23000') {
                $error_message = "Error: El ID del libro '$id' ya existe. Intenta con otro código.";
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
                    
                    <?php if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] == 0): ?>
                        <li><a href="usuarios.php">Usuarios</a></li>
                    <?php endif; ?>

                    <li class="user-welcome">Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?></li>
                    <li><a href="logout.php">Salir</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="form-container">
            <h2>Registrar Nuevo Libro</h2>
            
            <?php if ($error_message): ?>
                <div class="error-msg"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <form id="productForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                
                <div class="form-group">
                    <label for="title_id">Código del Libro (ID) <strong>*</strong></label>
                    <input type="text" id="title_id" name="title_id" required placeholder="Ej: BU1032" maxlength="6">
                    <small>Máximo 6 caracteres.</small>
                </div>

                <div class="form-group">
                    <label for="title">Título del Libro <strong>*</strong></label>
                    <input type="text" id="title" name="title" required placeholder="Ingresa el título">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="type">Tipo / Categoría <strong>*</strong></label>
                        <select id="type" name="type" required>
                            <option value="business">Negocios (Business)</option>
                            <option value="psychology">Psicología</option>
                            <option value="mod_cook">Cocina Moderna</option>
                            <option value="popular_comp">Computación Popular</option>
                            <option value="trad_cook">Cocina Tradicional</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="pub_id">Editorial</label>
                        <select id="pub_id" name="pub_id">
                            <?php foreach ($publishers as $pub): ?>
                                <option value="<?php echo $pub['pub_id']; ?>">
                                    <?php echo htmlspecialchars($pub['pub_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Precio ($)</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" placeholder="0.00">
                    </div>

                    <div class="form-group">
                        <label for="pubdate">Fecha de Publicación</label>
                        <input type="date" id="pubdate" name="pubdate" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes">Resumen / Notas</label>
                    <textarea id="notes" name="notes" rows="3" placeholder="Breve descripción..."></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Guardar Libro</button>
                    <a href="libros.php" class="btn-dashboard btn-historial" style="text-decoration:none; padding:10px;">Cancelar</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>