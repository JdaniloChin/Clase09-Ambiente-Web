<?php
session_start();
if(!isset($_SESSION['nombre_usuario'])){
    header("Location: index.php");
    exit();
}
   require_once("includes/conexion.php"); 

   //CRUD usuarios

   if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $id = $_POST['usuario_id'];
        $name= $_POST['nombre'];
        $usuario =  $_POST['usuario'];
        $email =  $_POST['email'];
        $pass =  $_POST['password'];
        $confirm =  $_POST['confirm'];
        $rol =  $_POST['rol'];
        $estado =  $_POST['estado'];
        $mensaje = "";
        $tipo_mensaje = "";



        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $mensaje = "Email invalido";
            $tipo_mensaje = "danger";
        }elseif($pass !== $confirm){
            $mensaje = "Contraseñas no coinciden ";
            $tipo_mensaje = "danger";
        }else {
            $pass_hash = password_hash($pass, PASSWORD_DEFAULT);

            if(!empty($id)){
                //update
                $sql = "UPDATE usuarios
                SET nombre = ?, usuario = ?, correo =?, rol=?, estado=?" .
                (!empty($pass) ? ", clave = ?" : "" ) . "
                WHERE id_usuario = ?";
                $stmt = $mysqli->prepare($sql);
                if(!empty($pass)){
                    $stmt->bind_param('ssssssi', $name,$usuario,$email,$rol,$estado,$pass_hash,$id);
                }else{
                   $stmt->bind_param('sssssi', $name,$email,$rol,$estado,$id); 
                }
                 $stmt->execute();
                 if($stmt->sqlstate == '00000'){
                    $mensaje = "Usuario actualizado correctamente";
                    $tipo_mensaje = "success";
                 }elseif($stmt->sqlstate > 0){
                    $mensaje = "Advertencia, usuario actualizado con el código de advertencia: " . $stmt->sqlstate;
                    $tipo_mensaje = "warning";
                 }else{
                    $mensaje = "Error, usuario no actualizado, código de error: " . $stmt->sqlstate;
                    $tipo_mensaje = "danger";

                 }
                 $stmt->close();
            }else{
                //CREATE->INSERT de un usuario
                $sql = 'INSERT INTO usuarios (nombre, usuario, clave, correo, rol, estado) VALUES (?,?,?,?,?,?)';
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param('ssssss',$name,$usuario,$pass_hash,$email,$rol,$estado);
                $stmt->execute();
                if($stmt->sqlstate == '00000'){
                    $mensaje = "Usuario creado correctamente";
                    $tipo_mensaje = "success";
                }elseif($stmt->sqlstate > 0 ){
                    $mensaje="Advertencia, usuario creado pero dio un mensaje: " . $stmt->sqlstate;
                    $tipo_mensaje = "warning";
                }else{
                    $mensaje = "Error, el usuario no se pudo crear, código de error: " . $stmt->sqlstate;
                    $tipo_mensaje = "danger";
                }
                $stmt->close();
            }    
        }
        $_SESSION['mensaje'] = $mensaje;
        $_SESSION['tipo_mensaje'] = $tipo_mensaje;
        $mysqli->close();
        header("Location: " .$_SERVER['PHP_SELF']);
        exit();

   }

   if(isset($_GET['eliminar'])){
    $id = $_GET['eliminar'];
    $sql= "DELETE FROM usuarios WHERE id_usuario = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $stmt->close();
    $mysqli->close();
    header("Location: " .$_SERVER['PHP_SELF']);
    exit();
   }
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./estilos/estilo.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <title>Inicio</title>
</head>

<body>
    <div class="container-fluid">
        <div class="row min-vh-100">
            <?php include 'includes/menu.php'; ?>
            <main class="col-md-9 p-4">
                <?php if(isset($_SESSION['mensaje'])):?>
                    <div class="alert alert-<?php echo $_SESSION['tipo_mensaje'];?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($_SESSION['mensaje']);?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php
                        //limpiar variables
                        unset($_SESSION['mensaje']);
                        unset($_SESSION['tipo_mensaje']);
                    endif;
                 ?>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3>Usuarios del sistema</h3>
                    <button class="btn btn-success mb-3" id="btnAgregar" data-bs-toggle="modal"
                        data-bs-target="#modalFormulario">Agregar Usuario</button>
                </div>
                <table class="table table-bordered table-striped" id="tablaUsuarios">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aquí se agregan los usuarios dinámicamente -->
                    </tbody>
                </table>
            </main>
            <footer class="text-center mt-3">
                &copy; 2025 - Desarrollado por Ambiente Web Cliente Servidor
            </footer>
        </div>
    </div>
    <div class="modal fade" id="modalFormulario" tabindex="-1" aria-labelledby="modalFormularioLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalFormularioLabel">Registro de usuarios</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formularioUsuarios" method="post">
                        <input type="hidden" id="usuario_id" name="usuario_id">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre Completo:</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Juan Pérez"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="usuario" class="form-label">Usuario:</label>
                            <input type="text" class="form-control" id="usuario" name="usuario"
                                placeholder="usr123" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico:</label>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="usuario@dominio.com" required>
                        </div>
                        <div class="mb3">
                            <label for="password" class="form-label">Contraseña:</label>
                            <input type="password" class="form-control" id="password" name="password"
                                required>
                        </div>
                        <div class="mb3">
                            <label for="confirm" class="form-label">Confirmar Contraseña:</label>
                            <input type="password" class="form-control" id="confirm" name="confirm"
                                required>
                        </div>
                        <div class="mb-3">
                                <label for="rol" class="form-label">Rol:</label>
                                <select class="form-select" id="rol" name="rol">
                                    <option selected>Seleccione el rol</option>
                                    <option value="Admin">Admin</option>
                                    <option value="Cliente">Cliente</option>
                                    <option value="Vendedor">Vendedor</option>
                                </select>
                        </div>
                        <div class="mb-3">
                                <label for="estado" class="form-label">Estado:</label>
                                <select class="form-select" id="estado" name="estado">
                                    <option selected>Seleccione el estado</option>
                                    <option value="A">Activo</option>
                                    <option value="I">Inactivo</option>
                                </select>
                        </div>
                        <button type="submit" class="btn btn-success" id="btnGuardar">Guardar Usuario</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="./javascript/usuarios.js"></script> 
</body>

</html>