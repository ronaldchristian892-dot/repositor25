<?php
// ¡ADVERTENCIA! ESTE MÉTODO DEJA LA CONTRASEÑA EN TEXTO PLANO EN LA DB.
// SOLO USAR PARA ASEGURAR QUE EL SISTEMA FUNCIONE.

include("conexion.php");
session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === TRUE) {
    header("location: dashboard.php");
    exit;
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Captura y sanitiza los datos
    $username = $conn->real_escape_string(trim($_POST['username']));
    $password_ingresada = $conn->real_escape_string($_POST['password']); // También sanitizamos la contraseña

    // Consulta para obtener el usuario y la contraseña en texto plano
    // ¡LA VALIDACIÓN SE HACE DIRECTAMENTE EN EL SQL!
    $sql = "SELECT id, username FROM usuarios WHERE username = '$username' AND password = '$password_ingresada'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
        $row = $result->fetch_assoc();
            
        // Éxito: Creamos variables de sesión
        $_SESSION['loggedin'] = TRUE;
        $_SESSION['username'] = $row['username'];
        
        header("location: dashboard.php"); // REDIRECCIÓN AL DASHBOARD
        exit;
            
    } else {
        // Error: Usuario o Contraseña incorrectos
        $mensaje = "<div class='alert alert-danger' role='alert'>Usuario o Contraseña incorrectos.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet"> 
    <style>
        body { 
            background-color: var(--apple-dark, #1d1d1f); 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh;
            color: var(--apple-dark);
        }
        .login-container { 
            max-width: 400px; 
            background: var(--card-bg, white); 
            padding: 40px; 
            border-radius: 10px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.5); 
        }
        .logo { 
            color: var(--apple-link, #0071e3); 
            font-size: 2.5rem; 
            margin-bottom: 10px; 
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="text-center">
            <i class="fab fa-apple logo"></i>
            <h2 class="mb-4 fw-bold">Acceso de Administración</h2>
        </div>
        
        <?php echo $mensaje; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            
            <div class="mb-3">
                <label for="username" class="form-label">Usuario</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
            </div>
            
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-sign-in-alt me-2"></i> Iniciar Sesión</button>
            </div>
            <div class="text-center mt-3">
                 <a href="index.php" class="text-muted small">Volver a la Tienda</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>