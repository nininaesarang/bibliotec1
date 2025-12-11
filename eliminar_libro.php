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
            alert('ERROR: No se recibió ningún ID de libro.');
            window.location.href = 'libros.php';
          </script>";
    exit;
}

$id_libro_a_eliminar = $_GET['id'];

try {
    $sql = "DELETE FROM titles WHERE title_id = :id";
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([':id' => $id_libro_a_eliminar]);

    echo "<script>
            alert('¡ÉXITO!\\n\\nEl libro ha sido eliminado correctamente del inventario.');
            window.location.href = 'libros.php';
          </script>";
    exit;

} catch (PDOException $e) {
    
    if ($e->getCode() == '23000') {
        echo "<script>
                alert('NO SE PUEDE ELIMINAR:\\n\\nEste libro no se puede borrar porque tiene registros vinculados:\\n\\n- Ventas registradas\\n- Autores asignados\\n- Stock en tiendas\\n\\nPrimero debes eliminar esos registros vinculados.');
                window.location.href = 'libros.php';
              </script>";
    } else {
        $mensaje_tecnico = addslashes($e->getMessage());
        echo "<script>
                alert('ERROR DE BASE DE DATOS:\\n\\n$mensaje_tecnico');
                window.location.href = 'libros.php';
              </script>";
    }
    exit;
}
?>