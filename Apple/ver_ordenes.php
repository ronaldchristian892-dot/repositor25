<?php
include("conexion.php");
session_start();

// Simulación de autenticación (Aquí deberías verificar si el usuario es Admin)
// if (!isset($_SESSION['es_admin']) || $_SESSION['es_admin'] !== true) { /* Redirigir */ }

// 1. CONSULTA PRINCIPAL: Obtener todas las órdenes
$sql_ordenes = "SELECT id_orden, nombre_cliente, correo_cliente, metodo_pago, total_orden, fecha_orden 
                FROM ordenes 
                ORDER BY fecha_orden DESC";
$resultado_ordenes = $conn->query($sql_ordenes);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Gestión de Órdenes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet"> 
    <style>
        body { background-color: #f8f9fa; }
        .ordenes-header { margin-top: 40px; margin-bottom: 30px; }
        .table-responsive { background-color: #fff; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .text-apple { color: #0071e3 !important; }
    </style>
</head>
<body>
    <div class="container">
        <div class="ordenes-header">
            <h1 class="fw-bold text-apple"><i class="fas fa-clipboard-list me-2"></i> Gestión de Órdenes</h1>
            <a href="dashboard.php" class="btn btn-secondary mt-2"><i class="fas fa-tachometer-alt me-2"></i> Volver al Dashboard</a>
        </div>
        
        <?php if ($resultado_ordenes->num_rows > 0): ?>
            <div class="table-responsive p-3">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID Orden</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Pago</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($orden = $resultado_ordenes->fetch_assoc()): ?>
                        <tr>
                            <td class="fw-bold text-apple">#<?php echo htmlspecialchars($orden['id_orden']); ?></td>
                            <td>
                                <?php echo htmlspecialchars($orden['nombre_cliente']); ?><br>
                                <small class="text-muted"><?php echo htmlspecialchars($orden['correo_cliente']); ?></small>
                            </td>
                            <td class="text-success fw-bold">$<?php echo number_format($orden['total_orden'], 2, '.', ','); ?></td>
                            <td><span class="badge bg-primary"><?php echo htmlspecialchars($orden['metodo_pago']); ?></span></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($orden['fecha_orden'])); ?></td>
                            <td>
                                <a href="ver_detalle_orden.php?id=<?php echo $orden['id_orden']; ?>" class="btn btn-sm btn-info text-white">
                                    <i class="fas fa-eye"></i> Ver Detalle
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center mt-5">
                <i class="fas fa-info-circle me-2"></i> Aún no hay órdenes registradas.
            </div>
        <?php endif; ?>
    </div>
    
    <?php $conn->close(); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>