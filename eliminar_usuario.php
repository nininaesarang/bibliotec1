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
            alert('ERROR: No se recibió ningún ID.');
            window.location.href = 'usuarios.php';
          </script>";
    exit;
}

$id_a_eliminar = $_GET['id'];
$id_actual = $_SESSION['user_id'];

if ($id_a_eliminar == $id_actual) {
    echo "<script>
            alert('SEGURIDAD:\\n\\nNo puedes eliminar tu propia cuenta mientras estás conectado.');
            window.location.href = 'usuarios.php';
          </script>";
    exit;
}

try {
    $sql = "DELETE FROM usuarios WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([':id' => $id_a_eliminar]);
    echo "<script>
            alert('¡ÉXITO!\\n\\nEl usuario ha sido eliminado correctamente.');
            window.location.href = 'usuarios.php';
          </script>";
    exit;

} catch (PDOException $e) {
    if ($e->getCode() == '23000') {
        echo "<script>
                alert('NO SE PUEDE ELIMINAR:\\n\\nEste usuario (Vendedor) ya tiene ventas o registros en el historial.\\n\\nPrimero debes borrar sus ventas para poder eliminar el usuario.');
                window.location.href = 'usuarios.php';
              </script>";
    } else {
        $mensaje_tecnico = addslashes($e->getMessage());
        echo "<script>
                alert('ERROR DE BASE DE DATOS:\\n\\n$mensaje_tecnico');
                window.location.href = 'usuarios.php';
              </script>";
    }
    exit;
}
?>