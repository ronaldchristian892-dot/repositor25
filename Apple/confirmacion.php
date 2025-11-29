<?php
session_start();
include("conexion.php"); // Incluimos la conexión

// 1. VALIDACIÓN
if (empty($_SESSION['carrito']) || $_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['finalizar_compra'])) {
    header("location: index.php");
    exit;
}

// 2. SANITIZAR DATOS DEL CLIENTE
$nombre = $conn->real_escape_string($_POST['nombre'] ?? 'N/A');
$correo = $conn->real_escape_string(filter_var($_POST['correo'] ?? 'N/A', FILTER_SANITIZE_EMAIL));
$telefono = $conn->real_escape_string($_POST['telefono'] ?? 'N/A');
$direccion = $conn->real_escape_string($_POST['direccion'] ?? 'N/A');
$metodo_pago = $conn->real_escape_string($_POST['metodo_pago'] ?? 'N/A');

// Copia del Carrito (antes de borrar la sesión)
$items_carrito = $_SESSION['carrito'];

// 3. CÁLCULO DE TOTALES
$subtotal = 0;
foreach ($items_carrito as $item) {
    $item_precio = (float)($item['precio'] ?? 0);
    $item_cantidad = (int)($item['cantidad'] ?? 0);
    $subtotal += $item_precio * $item_cantidad;
}
$impuesto = $subtotal * 0.18;
$total_a_pagar = $subtotal + $impuesto;

// 4. INSERCIÓN EN LA BASE DE DATOS (TRANSACCIÓN)

$conn->begin_transaction();
$id_orden = 0; 

try {
    // A) Insertar la ORDEN PRINCIPAL (tabla 'ordenes')
    $sql_orden = "INSERT INTO ordenes (nombre_cliente, correo_cliente, telefono_cliente, direccion_entrega, metodo_pago, total_orden, fecha_orden) VALUES (
        '$nombre', 
        '$correo', 
        '$telefono',
        '$direccion', 
        '$metodo_pago', 
        $total_a_pagar, 
        NOW()
    )";
    
    if (!$conn->query($sql_orden)) {
        throw new Exception("Error al insertar la orden maestra.");
    }
    
    $id_orden = $conn->insert_id;
    
    // B) Insertar los DETALLES DE LA ORDEN (tabla 'detalles_orden')
    foreach ($items_carrito as $item) {
        $id_producto = (int)($item['id'] ?? 0);
        $nombre_prod = $conn->real_escape_string($item['nombre']);
        $precio_unitario = (float)($item['precio'] ?? 0);
        $cantidad = (int)($item['cantidad'] ?? 0);
        
        $sql_detalle = "INSERT INTO detalles_orden (id_orden, id_producto, nombre_producto, precio_unitario, cantidad) VALUES (
            $id_orden,
            $id_producto,
            '$nombre_prod',
            $precio_unitario,
            $cantidad
        )";
        
        if (!$conn->query($sql_detalle)) {
            throw new Exception("Error al insertar el detalle.");
        }
    }
    
    // Si todo es exitoso, confirmar la transacción
    $conn->commit();
    
    // 5. LIMPIAR EL CARRITO DE LA SESIÓN
    unset($_SESSION['carrito']);

} catch (Exception $e) {
    // Si hay un error, deshacer todos los cambios
    $conn->rollback();
    $_SESSION['error_compra'] = "Hubo un error al procesar tu pedido.";
    header("location: carrito.php");
    exit;
}

// Usamos el ID de la orden real para la boleta
$order_id_boleta = $id_orden; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boleta de Venta - Pedido Confirmado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet"> 
    <style>
        body { background-color: #f0f0f5; }
        .boleta-card { 
            max-width: 800px; 
            margin: 50px auto; 
            background: #fff; 
            border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
            padding: 40px; 
        }
        .header-boleta { border-bottom: 3px solid #0071e3; padding-bottom: 15px; margin-bottom: 30px; }
        .footer-boleta { border-top: 1px dashed #ddd; padding-top: 20px; margin-top: 30px; }
        .text-apple { color: #0071e3 !important; }
    </style>
</head>
<body>
    <div class="boleta-card">
        <div class="header-boleta">
            <h2 class="fw-bold mb-1 text-apple"><i class="fas fa-apple-alt me-2"></i> Apple Store Perú</h2>
            <p class="mb-0 text-muted">Boleta de Venta Electrónica</p>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <h5 class="fw-bold">Detalles de la Orden</h5>
                <p class="mb-1"><strong>Nº de Orden:</strong> <span class="text-apple fw-bold"><?php echo $order_id_boleta; ?></span></p>
                <p class="mb-1"><strong>Fecha:</strong> <?php echo date('d/m/Y H:i:s'); ?></p>
                <p class="mb-1"><strong>Método de Pago:</strong> <span class="badge bg-success"><?php echo htmlspecialchars($metodo_pago); ?></span></p>
            </div>
            <div class="col-md-6">
                <h5 class="fw-bold">Datos del Cliente</h5>
                <p class="mb-1"><strong>Cliente:</strong> <?php echo htmlspecialchars($nombre); ?></p>
                <p class="mb-1"><strong>Correo:</strong> <?php echo htmlspecialchars($correo); ?></p>
                <p class="mb-1"><strong>Teléfono:</strong> <?php echo htmlspecialchars($telefono); ?></p>
                <p class="mb-1"><strong>Dirección:</strong> <?php echo htmlspecialchars($direccion); ?></p>
            </div>
        </div>

        <h5 class="fw-bold mb-3">Productos Adquiridos</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>Producto</th>
                        <th class="text-center">Cant.</th>
                        <th class="text-end">P. Unitario</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items_carrito as $item): ?>
                    <?php 
                        $item_precio = (float)($item['precio'] ?? 0);
                        $item_cantidad = (int)($item['cantidad'] ?? 0);
                        $item_total = $item_precio * $item_cantidad;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                        <td class="text-center"><?php echo $item_cantidad; ?></td>
                        <td class="text-end">$<?php echo number_format($item_precio, 2, '.', ','); ?></td>
                        <td class="text-end">$<?php echo number_format($item_total, 2, '.', ','); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="row justify-content-end mt-4">
            <div class="col-md-6">
                <div class="d-flex justify-content-between">
                    <span class="fw-normal">Subtotal:</span>
                    <span class="fw-bold">$<?php echo number_format($subtotal, 2, '.', ','); ?></span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="fw-normal">Impuesto (18%):</span>
                    <span class="fw-bold">$<?php echo number_format($impuesto, 2, '.', ','); ?></span>
                </div>
                <div class="d-flex justify-content-between fs-5 mt-2 p-2 bg-light rounded">
                    <span class="fw-bold">TOTAL PAGADO:</span>
                    <span class="fw-bold text-apple">$<?php echo number_format($total_a_pagar, 2, '.', ','); ?></span>
                </div>
            </div>
        </div>


        <div class="footer-boleta text-center text-muted">
            <p class="mb-1">¡Gracias por tu compra en Apple Store Perú!</p>
            <p class="mb-1 fw-bold">Se ha generado la Orden Nº <?php echo $order_id_boleta; ?> en el sistema.</p>
            <a href="index.php" class="btn btn-sm btn-outline-secondary mt-2"><i class="fas fa-home me-1"></i> Volver a la tienda</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>