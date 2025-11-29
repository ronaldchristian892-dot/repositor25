<?php
session_start();
include("conexion.php"); 

// Asegurar que solo se acceda si hay algo en el carrito
if (empty($_SESSION['carrito'])) {
    header("location: carrito.php");
    exit;
}

// Lógica para calcular totales 
$subtotal = 0;
foreach ($_SESSION['carrito'] as $item) {
    $item_precio = (float)($item['precio'] ?? 0);
    $item_cantidad = (int)($item['cantidad'] ?? 0);
    $subtotal += $item_precio * $item_cantidad;
}
$impuesto = $subtotal * 0.18;
$total_a_pagar = $subtotal + $impuesto;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Apple Store Perú</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet"> 
    <style>
        body { background-color: #f0f0f5; } 
        .checkout-container { margin-top: 50px; margin-bottom: 50px; }
        .card-checkout { border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .resumen-total { background-color: #fff; padding: 25px; border-radius: 12px; }
        .metodo-pago-box { border: 2px solid #ddd; border-radius: 8px; padding: 15px; margin-bottom: 10px; cursor: pointer; transition: all 0.2s; }
        .metodo-pago-box:hover, .metodo-pago-box.active { border-color: #0071e3; background-color: #e6f0ff; }
        .text-apple { color: #0071e3 !important; }
    </style>
</head>
<body>
    <div class="container checkout-container">
        <h1 class="mb-5 fw-bold text-center"><i class="fas fa-truck me-2"></i> Finalizar Compra</h1>
        
        <form action="confirmacion.php" method="POST">
            <div class="row g-5">
                <div class="col-lg-7">
                    <div class="card card-checkout p-4">
                        <h4 class="mb-4">1. Datos Personales y Envío</h4>
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre Completo</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo" name="correo" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" required>
                        </div>
                        <div class="mb-4">
                            <label for="direccion" class="form-label">Dirección de Entrega</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Ej: Av. Las Begonias 123, Urb. San Isidro" required>
                        </div>

                        <h4 class="mb-4 pt-3 border-top">2. Método de Pago</h4>
                        
                        <div class="metodo-pago-box" onclick="document.getElementById('pago_yape').checked = true;">
                            <input class="form-check-input float-end" type="radio" name="metodo_pago" id="pago_yape" value="Yape" required>
                            <label class="form-check-label fw-bold" for="pago_yape">
                                <i class="fas fa-mobile-alt me-2 text-apple"></i> Yape (Transferencia Rápida)
                            </label>
                        </div>

                        <div class="metodo-pago-box" onclick="document.getElementById('pago_plin').checked = true;">
                            <input class="form-check-input float-end" type="radio" name="metodo_pago" id="pago_plin" value="Plin">
                            <label class="form-check-label fw-bold" for="pago_plin">
                                <i class="fas fa-qrcode me-2 text-apple"></i> Plin (Interbancario Rápido)
                            </label>
                        </div>
                        
                        <div class="metodo-pago-box" onclick="document.getElementById('pago_tarjeta').checked = true;">
                            <input class="form-check-input float-end" type="radio" name="metodo_pago" id="pago_tarjeta" value="Tarjeta">
                            <label class="form-check-label fw-bold" for="pago_tarjeta">
                                <i class="fas fa-credit-card me-2 text-apple"></i> Tarjeta de Crédito/Débito
                            </label>
                        </div>

                        <div class="metodo-pago-box" onclick="document.getElementById('pago_contraentrega').checked = true;">
                            <input class="form-check-input float-end" type="radio" name="metodo_pago" id="pago_contraentrega" value="Contraentrega">
                            <label class="form-check-label fw-bold" for="pago_contraentrega">
                                <i class="fas fa-handshake me-2 text-apple"></i> Pago Contraentrega
                            </label>
                        </div>

                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="resumen-total shadow-sm">
                        <h4 class="mb-4">Resumen del Pedido</h4>
                        <ul class="list-group mb-4">
                            <?php foreach ($_SESSION['carrito'] as $item): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center p-2">
                                    <div class="me-auto">
                                        <span class="fw-medium"><?php echo htmlspecialchars($item['nombre']); ?></span>
                                        <small class="d-block text-muted">Cant: <?php echo htmlspecialchars($item['cantidad']); ?> x $<?php echo number_format($item['precio'], 2); ?></small>
                                    </div>
                                    <span class="fw-bold">$<?php echo number_format($item['precio'] * $item['cantidad'], 2); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <div class="d-flex justify-content-between fw-normal mb-2">
                            <span>Subtotal:</span>
                            <span>$<?php echo number_format($subtotal, 2, '.', ','); ?></span>
                        </div>
                        <div class="d-flex justify-content-between fw-normal mb-3">
                            <span>Impuesto (18%):</span>
                            <span>$<?php echo number_format($impuesto, 2, '.', ','); ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fs-4 fw-bold mb-4">
                            <span>Total Final:</span>
                            <span class="text-apple">$<?php echo number_format($total_a_pagar, 2, '.', ','); ?></span>
                        </div>

                        <button type="submit" name="finalizar_compra" class="btn btn-success w-100 btn-lg" style="border-radius: 50px;">
                            <i class="fas fa-check-circle me-2"></i> Confirmar y Pagar
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.metodo-pago-box').forEach(box => {
            box.addEventListener('click', function() {
                document.querySelectorAll('.metodo-pago-box').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>