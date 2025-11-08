<?php
    session_start();
    require_once("includes/conexion.php"); 

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $email = $_POST['email'];
        $pass = $_POST['password'];
        
        //validar datos
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $mensaje = "Correo Invalido";
        }else{
            //buscar si el correo existe en la base de datos
            $sql = "SELECT nombre, clave, rol FROM usuarios WHERE correo = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("s",$email);
            $stmt->execute();

            $resultado = $stmt->get_result();

            if($resultado->num_rows === 1){
                $usuario = $resultado->fetch_assoc();

                if(password_verify($pass, $usuario['clave'])){
                    $_SESSION['nombre_usuario'] = $usuario['nombre'];
                    $_SESSION['rol'] = $usuario['rol'];
                    $_SESSION['correo'] = $email;

                    header("Location: home.php");
                    exit();
                }

            }else{
                $mensaje = "Correo no registrado";
            }
            $stmt->close();
            $mysqli->close();
        }

    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <title>Inicio Sesión</title>
</head>
<body class="bg-light">
    <main>
        <!-- ajustar el form a la pantalla-->
        <div class="container d-flex justify-content-center align-items-center min-vh-100">
            <div class="card p-4 shadow-lg w-100" style="max-width: 400px;">
                <!--Titulo del form-->
                <h3 class="card-title text-center mb-4">Inicio Sesión</h3>
                <div class="card-body">
                    <form id="loginform" method="post">
                        <div class="mb-3">
                            <label class="form-label" form="email">Usuario:</label>
                            <input class="form-control" type="email" id="email" name="email" placeholder="test@example.com" required>
                        </div>
                        <label for="password" class="form-label">Contraseña:</label>
                        <div class="input-group mb-3">
                            <input id="password" name="password" type="password" class="form-control" placeholder="Introduce tu contraseña" aria-label="Contraseña" required>
                            <button type="button" class="btn btn.outline-secondary border rounded-2" id="togglePassword" aria-label="Mostrar Contraseña" title="Mostrar Contraseña">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                        <p class="text-center mt-3">Si no tiene cuenta <a href="#">Registrarse aquí.</a></p>
                    </form>
                    <div id="login-error" class="text-danger mt-3" style="display: none;">Correo o contraseña invalida.
                        <p class="text-center mt-3">Si no tiene cuenta <a href="#">Registrarse aquí.</a></p>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <footer class="text-center mt-3">
        &copy; 2025 - Desarrollado por Ambiente Web Cliente Servidor
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./javascript/login.js"></script>
</body>
</html>