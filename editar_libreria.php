<?php
// editar_libreria.php

// 1. INICIAR SESIÓN Y CONEXIÓN
session_start();
require 'conexion.php'; 

// 2. SEGURIDAD
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// 3. VALIDAR ID
if (!isset($_GET['id'])) {
    header("Location: librerias.php");
    exit;
}

$id_tienda = $_GET['id'];
$mensaje_error = "";

// 4. OBTENER DATOS ACTUALES (De la tabla STORES)
try {
    $sql = "SELECT * FROM stores WHERE stor_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id_tienda]);
    $tienda = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tienda) {
        header("Location: librerias.php?msg=error&detail=TiendaNoEncontrada");
        exit;
    }
} catch (PDOException $e) {
    die("Error al obtener datos: " . $e->getMessage());
}

// 5. PROCESAR FORMULARIO (UPDATE)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Recibimos los datos
    $nombre    = trim($_POST['stor_name']);
    $direccion = trim($_POST['stor_address']);
    $ciudad    = trim($_POST['city']);
    $estado    = trim($_POST['state']);
    $zip       = trim($_POST['zip']);

    if (empty($nombre) || empty($direccion) || empty($ciudad) || empty($zip)) {
        $mensaje_error = "Todos los campos obligatorios deben llenarse.";
    } else {
        try {
            // SQL UPDATE para STORES
            $sql_update = "UPDATE stores 
                           SET stor_name = :nom, 
                               stor_address = :dir, 
                               city = :cit, 
                               state = :sta, 
                               zip = :zip
                           WHERE stor_id = :id";
            
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->execute([
                ':nom' => $nombre,
                ':dir' => $direccion,
                ':cit' => $ciudad,
                ':sta' => !empty($estado) ? $estado : null,
                ':zip' => $zip,
                ':id'  => $id_tienda // El ID original para el WHERE
            ]);

            header("Location: librerias.php?msg=edit_ok");
            exit;

        } catch (PDOException $e) {
            $mensaje_error = "Error al actualizar: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Tienda</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        .error-msg {
            background-color: #f8d7da; color: #721c24; padding: 10px;
            border-radius: 5px; margin-bottom: 15px; text-align: center;
        }
        input[readonly] {
            background-color: #e9ecef; cursor: not-allowed; color: #6c757d;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <h1>BiblioTec - Librerías</h1>
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
            <h2>Editar Librería: <?php echo htmlspecialchars($tienda['stor_name']); ?></h2>
            
            <?php if ($mensaje_error): ?>
                <div class="error-msg"><?php echo $mensaje_error; ?></div>
            <?php endif; ?>

            <form action="editar_libreria.php?id=<?php echo $id_tienda; ?>" method="POST">
                
                <div class="form-group">
                    <label for="stor_id">ID Librería</label>
                    <input type="text" id="stor_id" name="stor_id" 
                           value="<?php echo htmlspecialchars($tienda['stor_id']); ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="stor_name">Nombre de la Librería</label>
                    <input type="text" id="stor_name" name="stor_name" required 
                           value="<?php echo htmlspecialchars($tienda['stor_name']); ?>">
                </div>

                <div class="form-group">
                    <label for="stor_address">Dirección</label>
                    <input type="text" id="stor_address" name="stor_address" required 
                           value="<?php echo htmlspecialchars($tienda['stor_address']); ?>">
                </div>

                <div class="form-group">
                    <label for="city">Ciudad</label>
                    <input type="text" id="city" name="city" required 
                           value="<?php echo htmlspecialchars($tienda['city']); ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="state">Estado</label>
                        <input type="text" id="state" name="state" maxlength="2"
                               value="<?php echo htmlspecialchars($tienda['state']); ?>">
                        <small>Máximo 2 letras.</small>
                    </div>
                    <div class="form-group">
                        <label for="zip">Código Postal</label>
                        <input type="text" id="zip" name="zip" required maxlength="5"
                               value="<?php echo htmlspecialchars($tienda['zip']); ?>">
                        <small>Máximo 5 números.</small>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Guardar Cambios</button>
                    <a href="librerias.php" class="btn-dashboard btn-historial">Cancelar</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>