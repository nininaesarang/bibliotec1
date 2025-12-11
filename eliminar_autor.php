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
            alert('ERROR: No se recibió ningún ID de autor.');
            window.location.href = 'autores.php';
          </script>";
    exit;
}

$id_autor_a_eliminar = $_GET['id'];

try {
    $sql = "DELETE FROM authors WHERE au_id = :id";
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([':id' => $id_autor_a_eliminar]);

    echo "<script>
            alert('¡ÉXITO!\\n\\nEl autor ha sido eliminado correctamente.');
            window.location.href = 'autores.php';
          </script>";
    exit;

} catch (PDOException $e) {
    
    if ($e->getCode() == '23000') {
        echo "<script>
                alert('NO SE PUEDE ELIMINAR:\\n\\nEste autor tiene libros asignados en el sistema.\\n\\nPara eliminarlo, primero debes quitarle la autoría de sus libros usando el botón \"Obras\".');
                window.location.href = 'autores.php';
              </script>";
    } else {
        $mensaje_tecnico = addslashes($e->getMessage());
        echo "<script>
                alert('ERROR DE BASE DE DATOS:\\n\\n$mensaje_tecnico');
                window.location.href = 'autores.php';
              </script>";
    }
    exit;
}
?>