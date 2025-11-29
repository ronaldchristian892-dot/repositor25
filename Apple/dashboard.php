<?php
include("conexion.php");
session_start();

// CONTROL DE ACCESO (Si no hay sesión, regresa al login)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== TRUE) {
    header("location: login.php");
    exit;
}

// Lógica para cerrar sesión
if (isset($_GET['logout'])) {
    session_destroy();
    header("location: login.php");
    exit;
}

// LÓGICA PARA LEER PRODUCTOS
$sql = "SELECT id, nombre, descripcion, precio, imagen FROM store ORDER BY id DESC"; 
$result = $conn->query($sql);

$mensaje = "";
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'updated') {
        $mensaje = "<div class='alert alert-success' role='alert'>✅ Producto actualizado correctamente.</div>";
    } elseif ($_GET['status'] == 'deleted') {
        $mensaje = "<div class='alert alert-success' role='alert'>🗑️ Producto eliminado correctamente.</div>";
    } elseif ($_GET['status'] == 'inserted') {
        $mensaje = "<div class='alert alert-success' role='alert'>✅ Producto insertado correctamente.</div>";
    } elseif ($_GET['status'] == 'error') {
        $mensaje = "<div class='alert alert-danger' role='alert'>❌ Ocurrió un error en la operación.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Administración de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet"> 
    <style>
        .navbar-dark { background-color: var(--apple-dark, #1d1d1f); }
        .dashboard-container { margin-top: 50px; }
        .producto-imagen-mini { width: 50px; height: 50px; object-fit: cover; border-radius: 5px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php"><i class="fab fa-apple"></i> Dashboard Admin</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <span class="nav-link text-white">Bienvenido, <b><?php echo htmlspecialchars($_SESSION['username']); ?></b></span>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-danger btn-sm text-white ms-3" href="?logout=true"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container dashboard-container">
    <h1 class="mb-4 fw-bold">Gestión de Productos (CRUD)</h1>
    
    <?php echo $mensaje; ?>

    <div class="mb-4 d-flex justify-content-between">
        <a href="insertar_producto_con_subida.php" class="btn btn-success btn-lg me-2"><i class="fas fa-plus-circle me-2"></i> Crear Nuevo Producto</a>
        
                <a href="ver_ordenes.php" class="btn btn-primary btn-lg me-auto"><i class="fas fa-clipboard-list me-1"></i> Ver Órdenes de Clientes</a>

        <a href="index.php" class="btn btn-outline-secondary btn-lg"><i class="fas fa-store me-2"></i> Ver Tienda Pública</a>
    </div>

    <div class="table-responsive bg-white p-3 rounded shadow-sm">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $precio_display = '$' . number_format($row['precio'], 2, '.', ','); 
                        $img_path = empty($row['imagen']) ? 'img/default.jpg' : htmlspecialchars($row['imagen']);

                        echo '
                        <tr>
                            <td>' . htmlspecialchars($row['id']) . '</td>
                            <td><img src="' . $img_path . '" class="producto-imagen-mini" alt="' . htmlspecialchars($row['nombre']) . '"></td>
                            <td>' . htmlspecialchars($row['nombre']) . '</td>
                            <td>' . $precio_display . '</td>
                            <td>' . htmlspecialchars(substr($row['descripcion'], 0, 50)) . '...</td>
                            <td>
                                <a href="update.php?id=' . htmlspecialchars($row['id']) . '" class="btn btn-sm btn-primary me-2" title="Editar"><i class="fas fa-edit"></i></a>
                                <a href="delete.php?id=' . htmlspecialchars($row['id']) . '" class="btn btn-sm btn-danger" title="Eliminar" onclick="return confirm(\'¿Estás seguro de que deseas eliminar este producto?\');"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        ';
                    }
                } else {
                    echo '<tr><td colspan="6" class="text-center">No hay productos en la base de datos.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
