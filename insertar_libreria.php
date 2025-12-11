<?php
session_start();
require 'conexion.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error_message = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id        = trim($_POST['stor_id']);
    $nombre    = trim($_POST['stor_name']);
    $direccion = trim($_POST['stor_address']);
    $ciudad    = trim($_POST['city']);
    $estado    = trim($_POST['state']);
    $zip       = trim($_POST['zip']);

    if (empty($id) || empty($nombre) || empty($direccion) || empty($ciudad) || empty($zip)) {
        $error_message = "Todos los campos son obligatorios (excepto estado si no aplica).";
    } else {
        try {
            $sql = "INSERT INTO stores (stor_id, stor_name, stor_address, city, state, zip) 
                    VALUES (:id, :nom, :dir, :cit, :sta, :zip)";
            
            $stmt = $pdo->prepare($sql);
            
            $stmt->execute([
                ':id'  => $id,
                ':nom' => $nombre,
                ':dir' => $direccion,
                ':cit' => $ciudad,
                ':sta' => !empty($estado) ? $estado : null,
                ':zip' => $zip
            ]);

            header("Location: librerias.php?msg=add_ok");
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                $error_message = "Error: El ID de tienda '$id' ya existe.";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Librería</title>
    <link rel="stylesheet" href="estilo.css"> 
    <style>
        .error-msg {
            background-color: #f8d7da; color: #721c24; padding: 10px;
            border: 1px solid #f5c6cb; border-radius: 5px; margin-bottom: 15px; text-align: center;
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
                    
                    <li class="user-welcome">
                        Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                    </li>
                    <li><a href="logout.php">Salir</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="form-container">
            <h2>Registrar Nueva Librería</h2>
            
            <?php if ($error_message): ?>
                <div class="error-msg"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                
                <div class="form-group">
                    <label for="stor_id">ID Librería <strong>*</strong></label>
                    <input type="text" id="stor_id" name="stor_id" required placeholder="Ej: 8080" maxlength="4">
                    <small>Máximo 4 caracteres.</small>
                </div>

                <div class="form-group">
                    <label for="stor_name">Nombre de la Librería<strong>*</strong></label>
                    <input type="text" id="stor_name" name="stor_name" required placeholder="Ej: Gandhi Centro">
                </div>

                <div class="form-group">
                    <label for="stor_address">Dirección</label>
                    <input type="text" id="stor_address" name="stor_address" required placeholder="Ej: Av. Vallarta 123">
                </div>

                <div class="form-group">
                    <label for="city">Ciudad</label>
                    <input type="text" id="city" name="city" required placeholder="Ej: Guadalajara">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="state">Estado</label>
                        <input type="text" id="state" name="state" placeholder="Ej: JC" maxlength="2">
                        <small>Máximo 2 letras.</small>
                    </div>
                    <div class="form-group">
                        <label for="zip">Código Postal</label>
                        <input type="text" id="zip" name="zip" required placeholder="Ej: 44100" maxlength="5">
                        <small>Máximo 5 números.</small>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Registrar librería</button>
                    <a href="librerias.php" class="btn-dashboard btn-historial">Cancelar</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>