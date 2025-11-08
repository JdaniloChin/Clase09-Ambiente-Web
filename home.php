<?php
session_start();
if(!isset($_SESSION['nombre_usuario'])){
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <title>Inicio</title>
</head>
<body>
    <div class="container-fluid">
        <div class="row min-vh-100">
            <?php include 'includes/menu.php'; ?>
            <main class="col-md-9 p-4">
                <h1 class="text-center">Bienvenido  <?php echo htmlspecialchars($_SESSION['nombre_usuario']);?></h1>
                <div class="alert alert-info text-center">
                    <p>El correo con el que se conectó es: <strong><?php echo htmlspecialchars($_SESSION['correo']);?></strong>.<br>
                    Su rol de seguridad es de: <strong><?php echo htmlspecialchars($_SESSION['rol']);?></strong>.</p>
                </div>
                
            </main>
            <footer class="text-center mt-3">
                &copy; 2025 - Desarrollado por Ambiente Web Cliente Servidor
            </footer>
        </div>
    </div>
</body>
</html>