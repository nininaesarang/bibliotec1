<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$nombre= htmlspecialchars($_SESSION['user_name']);
$rol = isset($_SESSION['user_rol']) ? $_SESSION['user_rol'] : 1;
$es_administrador = ($rol == 0)
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    
    <header>
        <div class="header-container">
            <h1>BiblioTec</h1>
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
        <h2 style="color: #2d3748;">Bienvenido</h2>

        <div class="dashboard-container">
            <div class="dashboard-item">
                <h3 style="color: #2d3748;">BiblioTec, biblioteca virtual</h3>
                <p>Sistema web de gestión de librerías.</p>
                <a href="generar_documentacion.php" class="btn-dashboard btn-productos">Documentación</a>
            </div>

            <div class="dashboard-item">
                <p><strong>Desarrollador:</strong> Maryjose Martínez Regalado</p>
                <p><strong>Correo elecetrónico:</strong> mmtz5818@gmail.com</p>
                <img src="img/libro.jpg" alt="BiblioTec">
            </div>
        </div>
    </main>
</body>
</html>