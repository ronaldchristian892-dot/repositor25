<?php
include("conexion.php");
session_start();

$id_orden = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_orden <= 0) {
    header("Location: ver_ordenes.php");
    exit;
}

$sql_orden = "SELECT * FROM ordenes WHERE id_orden = $id_orden";
$resultado_orden = $conn->query($sql_orden);

if ($resultado_orden->num_rows === 0) {
    header("Location: ver_ordenes.php");
    exit;
}

$orden = $resultado_orden->fetch_assoc();

$sql_detalles = "SELECT * FROM detalles_orden WHERE id_orden = $id_orden";
$resultado_detalles = $conn->query($sql_detalles);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Orden #<?php echo $id_orden; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet"> 
    <style>
        body { background-color: #f8f9fa; }
        .detalle-card { margin-top: 40px; margin-bottom: 50px; background: #fff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); padding: 30px; }
        .text-apple { color: #0071e3 !important; }
    </style>
</head>
<body>
    <div class="container">
        <div class="detalle-card">
            <h1 class="fw-bold mb-4">
                <i class="fas fa-receipt me-2 text-apple"></i> Orden #<?php echo htmlspecialchars($orden['id_orden']); ?>
            </h1>
            
            <a href="ver_ordenes.php" class="btn btn-secondary mb-4"><i class="fas fa-arrow-left me-2"></i> Volver a la Lista de Órdenes</a>

            <div class="row mb-5 border-bottom pb-3">
                <div class="col-md-6">
                    <h5 class="fw-bold text-apple">Datos del Cliente</h5>
                    <p class="mb-1"><strong>Nombre:</strong> <?php echo htmlspecialchars($orden['nombre_cliente']); ?></p>
                    <p class="mb-1"><strong>Correo:</strong> <?php echo htmlspecialchars($orden['correo_cliente']); ?></p>
                    <p class="mb-1"><strong>Teléfono:</strong> <?php echo htmlspecialchars($orden['telefono_cliente']); ?></p>
                </div>
                <div class="col-md-6">
                    <h5 class="fw-bold text-apple">Detalles del Envío y Pago</h5>
                    <p class="mb-1"><strong>Dirección:</strong> <?php echo htmlspecialchars($orden['direccion_entrega']); ?></p>
                    <p class="mb-1"><strong>Método Pago:</strong> <span class="badge bg-success"><?php echo htmlspecialchars($orden['metodo_pago']); ?></span></p>
                    <p class="mb-1"><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($orden['fecha_orden'])); ?></p>
                </div>
            </div>

            <h5 class="fw-bold mb-3">Productos Adquiridos</h5>
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID Producto</th>
                            <th>Nombre</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-end">Precio Unitario</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $subtotal_orden = 0;
                        while($detalle = $resultado_detalles->fetch_assoc()): 
                            $item_total = $detalle['precio_unitario'] * $detalle['cantidad'];
                            $subtotal_orden += $item_total;
                        ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($detalle['id_producto']); ?></td>
                            <td><?php echo htmlspecialchars($detalle['nombre_producto']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($detalle['cantidad']); ?></td>
                            <td class="text-end">$<?php echo number_format($detalle['precio_unitario'], 2, '.', ','); ?></td>
                            <td class="text-end">$<?php echo number_format($item_total, 2, '.', ','); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="row justify-content-end mt-4">
                <div class="col-md-6">
                    <div class="d-flex justify-content-between">
                        <span class="fw-normal">Subtotal de Productos:</span>
                        <span class="fw-bold">$<?php echo number_format($subtotal_orden, 2, '.', ','); ?></span>
                    </div>
                    <?php 
                        $impuesto_orden = $orden['total_orden'] - $subtotal_orden;
                    ?>
                    <div class="d-flex justify-content-between">
                        <span class="fw-normal">Impuesto Estimado:</span>
                        <span class="fw-bold">$<?php echo number_format($impuesto_orden, 2, '.', ','); ?></span>
                    </div>
                    <div class="d-flex justify-content-between fs-4 mt-2 p-2 bg-light rounded border border-primary">
                        <span class="fw-bold text-apple">TOTAL DE LA ORDEN:</span>
                        <span class="fw-bold text-apple">$<?php echo number_format($orden['total_orden'], 2, '.', ','); ?></span>
                    </div>
                </div>
            </div>

        </div>
    </div>
    
    <?php $conn->close(); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>