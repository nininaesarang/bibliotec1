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

$id_autor = $_GET['id'];
$mensaje_error = "";

try {
    $sql = "SELECT * FROM authors WHERE au_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id_autor]);
    $autor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$autor) {
        header("Location: autores.php?msg=error&detail=AutorNoEncontrado");
        exit;
    }
} catch (PDOException $e) {
    die("Error al cargar datos: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nombre   = trim($_POST['au_fname']);
    $apellido = trim($_POST['au_lname']);
    $telefono = trim($_POST['phone']);
    $calle    = trim($_POST['address']);
    $ciudad   = trim($_POST['city']);
    $estado   = trim($_POST['state']);
    $zip      = trim($_POST['zip']);
    $contrato = $_POST['contract'];

    if (empty($nombre) || empty($apellido) || empty($telefono)) {
        $mensaje_error = "Nombre, Apellido y Teléfono son obligatorios.";
    } else {
        try {
            $sql_update = "UPDATE authors 
                           SET au_fname = :fname, 
                               au_lname = :lname, 
                               phone = :phone, 
                               address = :addr, 
                               city = :cit, 
                               state = :sta, 
                               zip = :zip,
                               contract = :cont
                           WHERE au_id = :id";
            
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->execute([
                ':fname' => $nombre,
                ':lname' => $apellido,
                ':phone' => $telefono,
                ':addr'  => !empty($calle) ? $calle : null,
                ':cit'   => !empty($ciudad) ? $ciudad : null,
                ':sta'   => !empty($estado) ? $estado : null,
                ':zip'   => !empty($zip) ? $zip : null,
                ':cont'  => $contrato,
                ':id'    => $id_autor
            ]);

            header("Location: autores.php?msg=edit_ok");
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
    <title>Editar Autor</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        .error-msg {
            background-color: #f8d7da; color: #721c24; padding: 10px;
            border-radius: 5px; margin-bottom: 15px; text-align: center;
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
            <h2>Editar Autor: <?php echo htmlspecialchars($autor['au_fname'] . ' ' . $autor['au_lname']); ?></h2>
            
            <?php if ($mensaje_error): ?>
                <div class="error-msg"><?php echo $mensaje_error; ?></div>
            <?php endif; ?>

            <form action="editar_autor.php?id=<?php echo $id_autor; ?>" method="POST">
                
                <div class="form-group">
                    <label for="au_id">ID Autor</label>
                    <input type="text" id="au_id" name="au_id" 
                           value="<?php echo htmlspecialchars($autor['au_id']); ?>" readonly>
                    <small>El ID no se puede modificar.</small>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="au_fname">Nombre(s)</label>
                        <input type="text" id="au_fname" name="au_fname" required 
                               value="<?php echo htmlspecialchars($autor['au_fname']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="au_lname">Apellido(s)</label>
                        <input type="text" id="au_lname" name="au_lname" required 
                               value="<?php echo htmlspecialchars($autor['au_lname']); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone">Teléfono</label>
                    <input type="text" id="phone" name="phone" required maxlength="12"
                           value="<?php echo htmlspecialchars($autor['phone']); ?>">
                </div>

                <div class="form-group">
                    <label for="address">Dirección</label>
                    <input type="text" id="address" name="address" 
                           value="<?php echo htmlspecialchars($autor['address']); ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="city">Ciudad</label>
                        <input type="text" id="city" name="city" 
                               value="<?php echo htmlspecialchars($autor['city']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="state">Estado</label>
                        <input type="text" id="state" name="state" maxlength="2"
                               value="<?php echo htmlspecialchars($autor['state']); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="zip">Código Postal</label>
                        <input type="text" id="zip" name="zip" maxlength="5"
                               value="<?php echo htmlspecialchars($autor['zip']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="contract">¿Tiene Contrato?</label>
                        <select id="contract" name="contract">
                            <option value="1" <?php echo ($autor['contract'] == 1) ? 'selected' : ''; ?>>Sí</option>
                            <option value="0" <?php echo ($autor['contract'] == 0) ? 'selected' : ''; ?>>No</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Guardar Cambios</button>
                    <a href="autores.php" class="btn-dashboard btn-historial" style="text-decoration:none; padding:10px;">Cancelar</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>