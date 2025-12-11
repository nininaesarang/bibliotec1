<?php
session_start();
require 'conexion.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: autores.php");
    exit;
}

$au_id = $_GET['id'];
$mensaje = "";

$stmt_au = $pdo->prepare("SELECT au_fname, au_lname FROM authors WHERE au_id = ?");
$stmt_au->execute([$au_id]);
$autor = $stmt_au->fetch(PDO::FETCH_ASSOC);

if (!$autor) die("Autor no encontrado.");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $libros_seleccionados = $_POST['libros'] ?? [];

    try {
        $pdo->beginTransaction();

        $sql_delete = "DELETE FROM titleauthor WHERE au_id = :au_id";
        $stmt_del = $pdo->prepare($sql_delete);
        $stmt_del->execute([':au_id' => $au_id]);

        if (count($libros_seleccionados) > 0) {
            $sql_insert = "INSERT INTO titleauthor (au_id, title_id, au_ord, royaltyper) 
                           VALUES (:au_id, :title_id, 1, 50)";
            $stmt_ins = $pdo->prepare($sql_insert);

            foreach ($libros_seleccionados as $title_id) {
                $stmt_ins->execute([
                    ':au_id'    => $au_id,
                    ':title_id' => $title_id
                ]);
            }
        }

        $pdo->commit();
        $mensaje = "¡Bibliografía actualizada correctamente!";
        header("Location: autores.php?msg=biblio_ok");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        $mensaje = "Error al guardar: " . $e->getMessage();
    }
}
$sql = "SELECT 
            T.title_id, 
            T.title, 
            (SELECT COUNT(*) FROM titleauthor TA WHERE TA.title_id = T.title_id AND TA.au_id = :au_id) as asignado
        FROM titles T
        ORDER BY T.title ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute([':au_id' => $au_id]);
$catalogo_libros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asignar Obras</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        .checklist-container {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            background: #fff;
        }
        .book-item {
            display: flex;
            align-items: center;
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        .book-item:hover { background-color: #f9f9f9; }
        .book-item input[type="checkbox"] {
            transform: scale(1.5); /* Checkbox más grande */
            margin-right: 15px;
            cursor: pointer;
        }
        .book-item label {
            cursor: pointer;
            width: 100%;
            font-size: 1.1em;
        }
        .assigned-tag {
            background-color: #d4edda; color: #155724; 
            padding: 2px 6px; border-radius: 4px; font-size: 0.8em; margin-left: 10px;
        }
        .btn-historial {justify-content: center;display:block; text-align:center;}
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <h1>BiblioTec - Autores</h1>
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
        <div class="form-container" style="max-width: 700px;">
            <h2>Autor: <span style="color: #ea6666ff;"><?php echo htmlspecialchars($autor['au_fname'] . ' ' . $autor['au_lname']); ?></span></h2>
            <p>Selecciona los libros que ha escrito este autor.</p>

            <?php if ($mensaje): ?>
                <div style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; text-align: center; margin-bottom: 15px;">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                
                <div class="checklist-container">
                    <?php if (count($catalogo_libros) > 0): ?>
                        
                        <?php foreach ($catalogo_libros as $libro): ?>
                            <?php $isChecked = ($libro['asignado'] > 0) ? 'checked' : ''; ?>
                            
                            <div class="book-item">
                                <input type="checkbox" 
                                       id="book_<?php echo $libro['title_id']; ?>" 
                                       name="libros[]" 
                                       value="<?php echo $libro['title_id']; ?>"
                                       <?php echo $isChecked; ?>>
                                
                                <label for="book_<?php echo $libro['title_id']; ?>">
                                    <strong><?php echo htmlspecialchars($libro['title']); ?></strong>
                                    <span style="color:#888; font-size:0.9em;">(ID: <?php echo $libro['title_id']; ?>)</span>
                                    
                                    <?php if($isChecked): ?>
                                        <span class="assigned-tag">Asignado</span>
                                    <?php endif; ?>
                                </label>
                            </div>
                        <?php endforeach; ?>

                    <?php else: ?>
                        <p style="text-align:center; padding:20px;">No hay libros registrados en el sistema.</p>
                    <?php endif; ?>
                </div>

                <div class="form-actions" style="margin-top: 20px;">
                    <button type="submit" class="btn-primary" style="width: 100%;">Guardar Relación Autor-Libros</button>
                    <br><br>
                    <a href="autores.php" class="btn-dashboard btn-historial">Volver</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>