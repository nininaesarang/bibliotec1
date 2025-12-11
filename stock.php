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

$title_id = $_GET['id'];
$mensaje = "";

$stmt_book = $pdo->prepare("SELECT title FROM titles WHERE title_id = ?");
$stmt_book->execute([$title_id]);
$libro = $stmt_book->fetch(PDO::FETCH_ASSOC);

if (!$libro) die("Libro no encontrado.");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cantidades = $_POST['cantidades'];

    try {
        $pdo->beginTransaction();

        $sql_update = "UPDATE stockInStores SET qty = :qty WHERE stor_id = :stor_id AND title_id = :title_id";
        $stmt_update = $pdo->prepare($sql_update);
        
        $sql_insert = "INSERT INTO stockInStores (stor_id, title_id, qty) VALUES (:stor_id, :title_id, :qty)";
        $stmt_insert = $pdo->prepare($sql_insert);

        foreach ($cantidades as $stor_id => $qty) {
            $stmt_update->execute([
                ':qty' => $qty,
                ':stor_id' => $stor_id,
                ':title_id' => $title_id
            ]);
            if ($stmt_update->rowCount() == 0) {
                try {
                     $stmt_insert->execute([
                        ':stor_id' => $stor_id,
                        ':title_id' => $title_id,
                        ':qty' => $qty
                    ]);
                } catch(Exception $e) {
                }
            }
        }

        $pdo->commit();
        $mensaje = "¡Stock actualizado correctamente!";
   
        header("Location: libros.php?msg=stock_ok");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        $mensaje = "Error al guardar: " . $e->getMessage();
    }
}
$sql = "SELECT 
            S.stor_id, 
            S.stor_name, 
            COALESCE(SIS.qty, 0) as cantidad
        FROM stores S
        LEFT JOIN stockInStores SIS ON S.stor_id = SIS.stor_id AND SIS.title_id = :tid
        ORDER BY S.stor_name";

$stmt = $pdo->prepare($sql);
$stmt->execute([':tid' => $title_id]);
$tiendas_stock = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Stock</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        .stock-row { display: flex; justify-content: space-between; align-items: center; padding: 10px; border-bottom: 1px solid #eee; }
        .stock-row:hover { background-color: #f9f9f9; }
        .input-qty { width: 100px; padding: 5px; text-align: center; font-size: 1.1em; border: 1px solid #ccc; border-radius: 4px; }
        .store-name { font-weight: bold; font-size: 1.1em; }
        .btn-historial {justify-content: center;display:block; text-align:center;}
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <h1>BiblioTec - Stock</h1>
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
        <div class="form-container" style="max-width: 600px;">
            <h2>Libro: <span style="color: #ea6666ff;"><?php echo htmlspecialchars($libro['title']); ?></span></h2>
            <p>Asigna la cantidad de copias físicas disponibles en cada sucursal.</p>

            <?php if ($mensaje): ?>
                <p style="color: red; text-align: center;"><?php echo $mensaje; ?></p>
            <?php endif; ?>

            <form action="" method="POST">
                
                <div style="background: white; border: 1px solid #ddd; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                    <?php foreach ($tiendas_stock as $tienda): ?>
                        <div class="stock-row">
                            <div class="store-info">
                                <div class="store-name"><?php echo htmlspecialchars($tienda['stor_name']); ?></div>
                                <small style="color: #666;">ID: <?php echo $tienda['stor_id']; ?></small>
                            </div>
                            
                            <div>
                                <label>Cantidad:</label>
                                <input type="number" 
                                       class="input-qty"
                                       name="cantidades[<?php echo $tienda['stor_id']; ?>]" 
                                       value="<?php echo $tienda['cantidad']; ?>" 
                                       min="0">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary" style="width: 100%;">Guardar Cambios de Stock</button>
                    <br><br>
                    <a href="libros.php" class="btn-dashboard btn-historial">Cancelar</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>