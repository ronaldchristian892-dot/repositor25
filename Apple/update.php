<?php
include("conexion.php");
session_start();

// Control de Acceso
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== TRUE) {
    header("location: login.php");
    exit;
}

$mensaje = "";
$producto = null; 
$producto_id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);

if ($producto_id == 0) {
    header("location: dashboard.php?status=error");
    exit;
}

// LÓGICA DE ACTUALIZACIÓN
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_product'])) {
    
    $nombre = $conn->real_escape_string($_POST['txttitulo']);
    $descripcion = $conn->real_escape_string($_POST['txtdescripcion']);
    $precio = (float)$_POST['txtprecio'];
    $ruta_imagen_db = $conn->real_escape_string($_POST['current_imagen']);

    // Procesa nueva subida de imagen (opcional)
    if (!empty($_FILES['imagen']['name'])) {
        $upload_dir = "upload/";
        $nombre_archivo = basename($_FILES['imagen']['name']);
        $ruta_destino = $upload_dir . $nombre_archivo;
        $tipo_archivo = strtolower(pathinfo($ruta_destino, PATHINFO_EXTENSION));
        $permitidos = ["jpg", "png", "jpeg", "webp"]; 

        if (in_array($tipo_archivo, $permitidos)) {
            if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta_destino)) {
                $ruta_imagen_db = $ruta_destino;
            } else {
                $mensaje = "<div class='alert alert-warning' role='alert'>⚠️ Advertencia: No se pudo subir la nueva imagen.</div>";
            }
        } else {
            $mensaje = "<div class='alert alert-warning' role='alert'>⚠️ El formato de la nueva imagen no es compatible.</div>";
        }
    }
    
    // Ejecutar la actualización en la DB
    $sql_update = "UPDATE store SET 
                   nombre = '$nombre', 
                   descripcion = '$descripcion', 
                   precio = $precio, 
                   imagen = '$ruta_imagen_db'
                   WHERE id = $producto_id";

    if ($conn->query($sql_update) === TRUE) {
        header("location: dashboard.php?status=updated");
        exit;
    } else {
        $mensaje .= "<div class='alert alert-danger' role='alert'>❌ Error al actualizar: " . $conn->error . "</div>";
    }
}

// RECUPERAR DATOS DEL PRODUCTO
$sql_select = "SELECT id, nombre, descripcion, precio, imagen FROM store WHERE id = $producto_id";
$result_select = $conn->query($sql_select);

if ($result_select && $result_select->num_rows == 1) {
    $producto = $result_select->fetch_assoc();
} else {
    header("location: dashboard.php?status=error");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto #<?php echo $producto_id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f5f5f7; }
        .container { max-width: 600px; margin-top: 50px; margin-bottom: 50px; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .btn-primary { background-color: #0071e3; border-color: #0071e3; }
        .btn-primary:hover { background-color: #0077ff; border-color: #0077ff; }
        .current-image { max-width: 100%; height: auto; max-height: 200px; object-fit: contain; margin-top: 15px; border: 1px solid #eee; padding: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4 text-center"><i class="fas fa-edit me-2"></i> Editar Producto: <?php echo htmlspecialchars($producto['nombre']); ?></h2>
        
        <?php echo $mensaje; ?>

        <form action="update.php" method="post" enctype="multipart/form-data">
            
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($producto['id']); ?>">
            <input type="hidden" name="current_imagen" value="<?php echo htmlspecialchars($producto['imagen']); ?>">

            <div class="mb-3">
                <label for="txttitulo" class="form-label">Nombre del Producto</label>
                <input type="text" class="form-control" id="txttitulo" name="txttitulo" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="txtdescripcion" class="form-label">Descripción Breve</label>
                <textarea class="form-control" id="txtdescripcion" name="txtdescripcion" rows="2" required><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
            </div>

            <div class="mb-3">
                <label for="txtprecio" class="form-label">Precio (Solo número, usar punto para decimales)</label>
                <input type="number" step="0.01" class="form-control" id="txtprecio" name="txtprecio" value="<?php echo number_format($producto['precio'], 2, '.', ''); ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Imagen Actual:</label>
                <div class="text-center">
                    <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" class="current-image img-fluid" alt="Imagen actual del producto">
                </div>
                
                <label for="imagen" class="form-label mt-3">Reemplazar Imagen (Opcional)</label>
                <input type="file" class="form-control" id="imagen" name="imagen">
                <small class="form-text text-muted">Deja vacío para mantener la imagen actual.</small>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" name="update_product" class="btn btn-primary btn-lg"><i class="fas fa-save me-2"></i> Guardar Cambios</button>
                <a href="dashboard.php" class="btn btn-outline-secondary">Volver al Dashboard</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>