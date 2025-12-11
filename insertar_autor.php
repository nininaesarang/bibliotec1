<?php
session_start();
require 'conexion.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error_message = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id       = trim($_POST['au_id']);
    $nombre   = trim($_POST['au_fname']);
    $apellido = trim($_POST['au_lname']);
    $telefono = trim($_POST['phone']);
    $calle    = trim($_POST['address']);
    $ciudad   = trim($_POST['city']);
    $estado   = trim($_POST['state']);
    $zip      = trim($_POST['zip']);
    $contrato = $_POST['contract'];

    if (empty($id) || empty($nombre) || empty($apellido) || empty($telefono)) {
        $error_message = "El ID, Nombre, Apellido y Teléfono son obligatorios.";
    } else {
        try {
            $sql = "INSERT INTO authors (au_id, au_lname, au_fname, phone, address, city, state, zip, contract) 
                    VALUES (:id, :lname, :fname, :phone, :addr, :cit, :sta, :zip, :cont)";
            
            $stmt = $pdo->prepare($sql);
            
            $stmt->execute([
                ':id'    => $id,
                ':lname' => $apellido,
                ':fname' => $nombre,
                ':phone' => $telefono,
                ':addr'  => !empty($calle) ? $calle : null,
                ':cit'   => !empty($ciudad) ? $ciudad : null,
                ':sta'   => !empty($estado) ? $estado : null,
                ':zip'   => !empty($zip) ? $zip : null,
                ':cont'  => $contrato
            ]);

            header("Location: autores.php?msg=add_ok");
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                $error_message = "Error: El ID de autor '$id' ya existe.";
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
    <title>Registrar Autor</title>
    <link rel="stylesheet" href="estilo.css"> 
    <style>
        .error-msg {
            background-color: #f8d7da; color: #721c24; padding: 10px;
            border: 1px solid #f5c6cb; border-radius: 5px; margin-bottom: 15px; text-align: center;
        }
        .form-group input, .form-group select {
            width: 100%; padding: 10px 12px; border: 1px solid #ccc;
            border-radius: 4px; box-sizing: border-box; font-size: 15px;
        }
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
        <div class="form-container">
            <h2>Registrar Nuevo Autor</h2>
            
            <?php if ($error_message): ?>
                <div class="error-msg"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                
                <div class="form-group">
                    <label for="au_id">ID Autor <strong>*</strong></label>
                    <input type="text" id="au_id" name="au_id" required placeholder="Ej: 123-45-6789" maxlength="11">
                    <small>Formato sugerido: XXX-XX-XXXX (Max 11 chars)</small>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="au_fname">Nombre(s) <strong>*</strong></label>
                        <input type="text" id="au_fname" name="au_fname" required placeholder="Ej: Stephen">
                    </div>
                    <div class="form-group">
                        <label for="au_lname">Apellido(s) <strong>*</strong></label>
                        <input type="text" id="au_lname" name="au_lname" required placeholder="Ej: King">
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone">Teléfono <strong>*</strong></label>
                    <input type="text" id="phone" name="phone" required placeholder="Ej: 415 555-1234" maxlength="12">
                    <small>Formato sugerido: XXX XXX-XXXX (Max 12 chars)</small>
                </div>

                <div class="form-group">
                    <label for="address">Dirección</label>
                    <input type="text" id="address" name="address" placeholder="Calle y número">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="city">Ciudad</label>
                        <input type="text" id="city" name="city" placeholder="Ej: Oakland">
                    </div>
                    <div class="form-group">
                        <label for="state">Estado</label>
                        <input type="text" id="state" name="state" placeholder="CA" maxlength="2">
                        <small>Máximo 2 letras</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="zip">Código Postal</label>
                        <input type="text" id="zip" name="zip" placeholder="94609" maxlength="5">
                        <small>Máximo 5 números</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="contract">¿Tiene Contrato?</label>
                        <select id="contract" name="contract">
                            <option value="1">Sí (Con contrato)</option>
                            <option value="0">No (Sin contrato)</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Registrar Autor</button>
                    <a href="autores.php" class="btn-dashboard btn-historial">Cancelar</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>