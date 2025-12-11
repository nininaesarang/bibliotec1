<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require 'conexion.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "<script>
            alert('ERROR: No se recibió ningún ID de tienda.');
            window.location.href = 'librerias.php';
          </script>";
    exit;
}

$id_tienda_a_eliminar = $_GET['id'];

try {
    $sql = "DELETE FROM stores WHERE stor_id = :id";
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([':id' => $id_tienda_a_eliminar]);

    // ÉXITO
    echo "<script>
            alert('¡ÉXITO!\\n\\nLa librería ha sido eliminada correctamente.');
            window.location.href = 'librerias.php';
          </script>";
    exit;

} catch (PDOException $e) {
    
    if ($e->getCode() == '23000') {
        echo "<script>
                alert('NO SE PUEDE ELIMINAR:\\n\\nEsta librería tiene registros vinculados:\\n\\n1. VENTAS (Tabla sales)\\n2. INVENTARIO (Tabla stockInStores)\\n3. DESCUENTOS (Tabla discounts)\\n\\nDebes eliminar primero esos registros antes de borrar la librería.');
                window.location.href = 'librerias.php';
              </script>";
    } else {
        $mensaje_tecnico = addslashes($e->getMessage());
        echo "<script>
                alert('ERROR DE BASE DE DATOS:\\n\\n$mensaje_tecnico');
                window.location.href = 'librerias.php';
              </script>";
    }
    exit;
}
?>