<?php 
// 🔑 PARTE CRÍTICA 1: INICIO DE SESIÓN Y CONEXIÓN
session_start(); // NECESARIO para que la variable $_SESSION['carrito'] funcione
include("conexion.php"); 

// Lógica para obtener productos (LIMIT 6)
$sql = "SELECT id, nombre, descripcion, precio, imagen FROM store LIMIT 6"; 
$result = $conn->query($sql);

// Contador del carrito para la navegación (si no existe, cuenta 0)
$carrito_count = count($_SESSION['carrito'] ?? []);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Apple Store Perú | La Innovación Definitiva en Tecnología</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

    <header>
        <nav class="navbar navbar-expand-lg navbar-dark sticky-top" aria-label="Navegación Principal de la Tienda Apple">
            <div class="container">
                <a class="navbar-brand" href="#home"><i class="fab fa-apple"></i> Apple Store Perú</a>
                <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="#home">Inicio</a></li>
                        <li class="nav-item"><a class="nav-link" href="#about">Nosotros</a></li>
                        <li class="nav-item"><a class="nav-link" href="#experiencia">Experiencia</a></li>
                        <li class="nav-item"><a class="nav-link" href="#productos">Productos</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Ofertas</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Contacto</a></li>
                        
                        <li class="nav-item">
                            <a href="carrito.php" class="nav-link btn btn-sm btn-primary ms-3">
                                <i class="fas fa-shopping-cart"></i>
                                Carrito (<?php echo $carrito_count; ?>)
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <section class="hero d-flex align-items-center" id="home" aria-labelledby="heroHeading">
            <div class="container text-center">
                <div class="hero-content animate__animated animate__fadeIn">
                    <h1 id="heroHeading" class="display-1 fw-bolder animate__fadeInDown">Bienvenido a Apple Store</h1>
                    <p class="lead fw-light animate__fadeInUp">La tecnología que redefine tu mundo. Solo en Perú.</p>
                    <a href="#productos" class="btn btn-outline-light btn-lg mt-4 animate__zoomIn scroll-link pulse-btn">Explorar Productos</a>
                </div>
            </div>
        </section>

        <section id="about" class="container-fluid py-5 bg-white">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6 order-lg-2">
                        <img class="img-fluid rounded shadow-lg" src="img/about.jpg" alt="Expertos en Apple Store Perú brindando soporte técnico.">
                    </div>
                    <div class="col-lg-6 order-lg-1">
                        <h4 class="text-primary mb-2">Acerca de Nosotros</h4>
                        <h2 class="display-6 mb-4 fw-bold">Llevando la innovación de Apple a Perú</h2>
                        <p class="mb-4 text-muted">En Apple Store Perú ofrecemos la experiencia completa de Apple: últimos productos, soporte oficial y expertos en tecnología dedicados a darte el mejor servicio.</p>
                        <div class="row pt-3">
                            <div class="col-sm-6">
                                <p class="fw-bold"><i class="fa fa-check-circle text-primary me-2"></i>Productos 100% originales</p>
                                <p class="fw-bold"><i class="fa fa-check-circle text-primary me-2"></i>Expertos Certificados Apple</p>
                            </div>
                            <div class="col-sm-6">
                                <p class="fw-bold"><i class="fa fa-check-circle text-primary me-2"></i>Garantía y soporte oficial</p>
                                <p class="fw-bold"><i class="fa fa-check-circle text-primary me-2"></i>Entrega Rápida y Segura</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="experiencia" class="container-fluid py-5 bg-light-subtle">
            <div class="container text-center">
                <h4 class="text-primary mb-2">Valor Agregado</h4>
                <h2 class="display-6 mb-5 fw-bold">La Experiencia Apple Definitiva</h2>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="p-4 bg-white rounded shadow-sm h-100">
                            <i class="fas fa-microchip fa-3x text-primary mb-3"></i>
                            <h5 class="fw-bold">Tecnología de Punta</h5>
                            <p class="text-muted">Accede siempre a los últimos chips y dispositivos de Apple tan pronto como son lanzados.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-4 bg-white rounded shadow-sm h-100">
                            <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                            <h5 class="fw-bold">Soporte Certificado</h5>
                            <p class="text-muted">Toda nuestra garantía y soporte es manejado por técnicos oficiales, dándote total tranquilidad.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-4 bg-white rounded shadow-sm h-100">
                            <i class="fas fa-handshake fa-3x text-primary mb-3"></i>
                            <h5 class="fw-bold">Financiamiento Flexible</h5>
                            <p class="text-muted">Planes de pago a meses sin intereses con las principales entidades bancarias de Perú.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <section id="productos" class="container-fluid products pb-5 pt-5">
            <div class="container products-mini">
                <div class="mx-auto text-center mb-5" style="max-width: 700px;">
                    <h4 class="text-primary mb-2">Productos Destacados</h4>
                    <h2 class="display-6 mb-5 fw-bold">Explora la vanguardia de la innovación</h2>
                    <p class="mb-0 text-muted">Los artículos más populares y mejor valorados por nuestros clientes.</p>
                </div>

                <div class="row g-4">
                    <?php
                    // Lógica para cargar productos desde la Base de Datos
                    if ($result && $result->num_rows > 0) {
                        // Iterar sobre los resultados de la consulta
                        while($row = $result->fetch_assoc()) {
                            // Preparamos variables con htmlspecialchars para seguridad
                            $id_html = htmlspecialchars($row['id']);
                            $nombre_html = htmlspecialchars($row['nombre']);
                            $desc_html = htmlspecialchars($row['descripcion']);
                            $img_html = htmlspecialchars($row['imagen']);
                            $precio_db = htmlspecialchars($row['precio']); // Precio sin formato para el POST
                            $precio_display = '$' . number_format($row['precio'], 2, '.', ','); // Precio formateado para mostrar

                            // El bloque HTML por cada producto
                            echo '
                            <div class="col-sm-6 col-lg-4">
                                <div class="product-item h-100 animate__animated animate__fadeInUp">
                                    <div class="product-img"><img src="' . $img_html . '" class="img-fluid w-100 h-100" alt="' . $nombre_html . '"></div>
                                    <div class="p-4 d-flex flex-column justify-content-between">
                                        <div>
                                            <h5>' . $nombre_html . '</h5>
                                            <p class="text-muted">' . $desc_html . '</p>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="product-price mb-0">' . $precio_display . '</h6>
                                            
                                            <form method="POST" action="carrito.php">
                                                <input type="hidden" name="producto_id" value="' . $id_html . '">
                                                <input type="hidden" name="nombre" value="' . $nombre_html . '">
                                                <input type="hidden" name="precio" value="' . $precio_db . '">
                                                <button type="submit" name="agregar" class="btn btn-primary"><i class="fas fa-cart-plus me-1"></i> Añadir</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            ';
                        }
                    } else {
                        // Mensaje si no hay productos o la consulta falló
                        echo '<div class="col-12 text-center"><p>No hay productos disponibles actualmente en la base de datos o la tabla "store" está vacía.</p></div>';
                    }
                    ?>
                </div>
            </div>
        </section>
    </main>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="cartToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="fas fa-shopping-cart text-primary me-2"></i>
                <strong class="me-auto">Apple Store Perú</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                </div>
        </div>
    </div>
    
    <footer class="bg-dark text-white pt-5 pb-3">
        <div class="container">
            <div class="row g-4 border-bottom pb-4 mb-4">
                
                <div class="col-md-4">
                    <h5 class="text-primary mb-3"><i class="fab fa-apple"></i> Apple Store Perú</h5>
                    <ul class="list-unstyled footer-links">
                        <li><a href="#home">Inicio</a></li>
                        <li><a href="#about">Acerca de Nosotros</a></li>
                        <li><a href="#experiencia">Servicios</a></li>
                        <li><a href="#productos">Explorar Productos</a></li>
                    </ul>
                </div>
                
                <div class="col-md-4">
                    <h5 class="text-primary mb-3">Soporte</h5>
                    <ul class="list-unstyled footer-links">
                        <li><a href="#">Contacto y Ubicación</a></li>
                        <li><a href="#">Centro de Ayuda</a></li>
                        <li><a href="#">Estado de tu Pedido</a></li>
                        <li><a href="#">Política de Garantía</a></li>
                    </ul>
                </div>

                <div class="col-md-4">
                    <h5 class="text-primary mb-3">Conéctate</h5>
                    <p class="text-muted small">Síguenos en nuestras redes para ver las últimas novedades de Apple.</p>
                    <div class="social-links-footer mt-3">
                        <a href="#" aria-label="Síguenos en Facebook"><i class="fab fa-facebook-f fa-lg me-3"></i></a>
                        <a href="#" aria-label="Síguenos en Instagram"><i class="fab fa-instagram fa-lg me-3"></i></a>
                        <a href="#" aria-label="Síguenos en Twitter"><i class="fab fa-twitter fa-lg me-3"></i></a>
                        <a href="#" aria-label="Mira nuestro canal de YouTube"><i class="fab fa-youtube fa-lg"></i></a>
                    </div>
                </div>

            </div>
            
            <div class="text-center">
                <p class="mb-0 small text-muted">&copy; 2025 Apple Store Perú. Todos los derechos reservados.</p>
            </div>
            
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>