<?php
session_start();
include("conexion.php"); 

// Inicializar el carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// LÓGICA 1: AGREGAR PRODUCTO (viene de index.php)
if (isset($_POST['agregar'])) {
    $id = (int)$_POST['producto_id'];
    $nombre = $_POST['nombre'];
    $precio = (float)$_POST['precio'];

    $encontrado = false;
    foreach ($_SESSION['carrito'] as $key => $item) {
        if ($item['id'] === $id) {
            $_SESSION['carrito'][$key]['cantidad']++; 
            $encontrado = true;
            break;
        }
    }

    if (!$encontrado) {
        $_SESSION['carrito'][] = [
            'id' => $id,
            'nombre' => $nombre,
            'precio' => $precio,
            'cantidad' => 1
        ];
    }
    
    // Redirige al carrito para evitar reenvío del formulario al recargar
    header("location: carrito.php");
    exit;
}

// LÓGICA 2: ELIMINAR PRODUCTO
if (isset($_GET['eliminar'])) {
    $item_key = (int)$_GET['eliminar'];
    if (isset($_SESSION['carrito'][$item_key])) {
        unset($_SESSION['carrito'][$item_key]); 
    }
    $_SESSION['carrito'] = array_values($_SESSION['carrito']); // Reindexar
    header("location: carrito.php");
    exit;
}

// LÓGICA 3: CALCULAR TOTALES
$subtotal = 0;
foreach ($_SESSION['carrito'] as $item) {
    $subtotal += $item['precio'] * $item['cantidad'];
}

$impuesto = $subtotal * 0.18; // 18% IGV/IVA
$total_a_pagar = $subtotal + $impuesto;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras Profesional</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet"> 
    <style>
        .carrito-container { margin-top: 50px; margin-bottom: 50px; }
        .resumen-card { background-color: #f5f5f7; border-radius: 10px; padding: 20px; }
    </style>
</head>
<body>
    <div class="container carrito-container">
        <h1 class="mb-4 fw-bold"><i class="fas fa-shopping-cart me-2"></i> Tu Carrito de Compras</h1>
        
        <a href="index.php" class="btn btn-outline-secondary mb-4"><i class="fas fa-arrow-left me-2"></i> Volver a la Tienda</a>

        <?php if (empty($_SESSION['carrito'])): ?>
            <div class="alert alert-info text-center" role="alert">
                Tu carrito está vacío. ¡Explora nuestros productos!
            </div>
        <?php else: ?>
        
        <div class="row">
            <div class="col-lg-8">
                <div class="table-responsive p-3 bg-white rounded shadow-sm">
                    <table class="table table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Producto</th>
                                <th>Precio Unitario</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th>Eliminar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($_SESSION['carrito'] as $key => $item): ?>
                            <?php $item_total = $item['precio'] * $item['cantidad']; ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                                <td>$<?php echo number_format($item['precio'], 2, '.', ','); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($item['cantidad']); ?>
                                </td>
                                <td>$<?php echo number_format($item_total, 2, '.', ','); ?></td>
                                <td>
                                    <a href="carrito.php?eliminar=<?php echo $key; ?>" class="btn btn-sm btn-danger" title="Eliminar Producto" onclick="return confirm('¿Seguro que quieres eliminar este producto?');">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="resumen-card">
                    <h4 class="mb-3">Resumen de la Compra</h4>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal (sin impuestos):</span>
                        <strong>$<?php echo number_format($subtotal, 2, '.', ','); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Impuesto (18%):</span>
                        <strong>$<?php echo number_format($impuesto, 2, '.', ','); ?></strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fs-5 fw-bold">TOTAL A PAGAR:</span>
                        <strong class="fs-5 text-primary">$<?php echo number_format($total_a_pagar, 2, '.', ','); ?></strong>
                    </div>
                    
                                        <a href="checkout.php" class="btn btn-success w-100 btn-lg mb-2" style="border-radius: 50px;">
                        <i class="fas fa-credit-card me-2"></i> Finalizar Compra
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>