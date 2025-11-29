<?php
include("conexion.php");
session_start();

// Control de Acceso
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== TRUE) {
    header("location: login.php");
    exit;
}

// Obtener y sanitizar el ID
$producto_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($producto_id > 0) {
    // 1. Obtener la ruta de la imagen
    $sql_get_image = "SELECT imagen FROM store WHERE id = $producto_id";
    $result_image = $conn->query($sql_get_image);

    if ($result_image && $result_image->num_rows == 1) {
        $row = $result_image->fetch_assoc();
        $ruta_imagen = $row['imagen'];

        // 2. Eliminar el registro de la base de datos
        $sql_delete = "DELETE FROM store WHERE id = $producto_id";

        if ($conn->query($sql_delete) === TRUE) {
            
            // 3. Eliminar el archivo físico del servidor
            // Se comprueba que la ruta comience con 'upload/' por seguridad
            if (!empty($ruta_imagen) && file_exists($ruta_imagen) && strpos($ruta_imagen, 'upload/') === 0) {
                unlink($ruta_imagen);
            }
            
            header("location: dashboard.php?status=deleted");
            exit;
        }
    }
}

// Redirigir con error si algo falló
header("location: dashboard.php?status=error");
exit;
?>