<?php
include("conexion.php");
session_start();

// Control de Acceso
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== TRUE) {
    header("location: login.php");
    exit;
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $upload_dir = "upload/"; 
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $nombre = $conn->real_escape_string($_POST['txttitulo']);
    $descripcion = $conn->real_escape_string($_POST['txtdescripcion']);
    $precio = (float)$_POST['txtprecio'];
    
    $ruta_imagen_db = "";

    if (!empty($_FILES['imagen']['name'])) {
        $nombre_archivo = basename($_FILES['imagen']['name']);
        $ruta_destino = $upload_dir . $nombre_archivo;
        $tipo_archivo = strtolower(pathinfo($ruta_destino, PATHINFO_EXTENSION));

        $permitidos = ["jpg", "png", "jpeg", "webp"]; 
        
        if (in_array($tipo_archivo, $permitidos)) {
            if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta_destino)) {
                $ruta_imagen_db = $ruta_destino;
            } else {
                $mensaje = "<div class='alert alert-warning' role='alert'>⚠️ Advertencia: No se pudo mover el archivo de imagen al servidor.</div>";
            }
        } else {
            $mensaje = "<div class='alert alert-warning' role='alert'>⚠️ El formato de archivo no es compatible. Solo se permiten JPG, PNG, JPEG, WEBP.</div>";
        }
    }

    if (empty($mensaje) || strpos($mensaje, 'Advertencia') !== false) {
        $sql = "INSERT INTO store (nombre, descripcion, precio, imagen) 
                VALUES ('$nombre', '$descripcion', $precio, '$ruta_imagen_db')";

        if ($conn->query($sql) === TRUE) {
            header("location: dashboard.php?status=inserted");
            exit;
        } else {
            $mensaje = "<div class='alert alert-danger' role='alert'>❌ Error al insertar en la base de datos: " . $conn->error . "</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insertar Nuevo Producto (Con Subida de Imagen)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f5f5f7; }
        .container { max-width: 600px; margin-top: 50px; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .btn-primary { background-color: #0071e3; border-color: #0071e3; }
        .btn-primary:hover { background-color: #0077ff; border-color: #0077ff; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4 text-center"><i class="fas fa-box me-2"></i> Insertar Producto Apple Store</h2>
        
        <?php echo $mensaje; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            
            <div class="mb-3">
                <label for="txttitulo" class="form-label">Nombre del Producto</label>
                <input type="text" class="form-control" id="txttitulo" name="txttitulo" required>
            </div>

            <div class="mb-3">
                <label for="txtdescripcion" class="form-label">Descripción Breve</label>
                <textarea class="form-control" id="txtdescripcion" name="txtdescripcion" rows="2" required></textarea>
            </div>

            <div class="mb-3">
                <label for="txtprecio" class="form-label">Precio (Solo número, usar punto para decimales)</label>
                <input type="number" step="0.01" class="form-control" id="txtprecio" name="txtprecio" required>
            </div>
            
            <div class="mb-3">
                <label for="imagen" class="form-label">Subir Imagen del Producto</label>
                <input type="file" class="form-control" id="imagen" name="imagen" required>
                <small class="form-text text-muted">Formatos permitidos: JPG, PNG, JPEG, WEBP.</small>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-upload me-2"></i> Guardar y Subir Producto</button>
                <a href="dashboard.php" class="btn btn-outline-secondary">Volver al Dashboard</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>