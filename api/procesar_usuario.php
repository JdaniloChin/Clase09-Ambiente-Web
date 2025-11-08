<?php
    require_once("../includes/conexion.php"); 

      if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $id = $_POST['usuario_id'];
        $name= $_POST['nombre'];
        $usuario =  $_POST['usuario'];
        $email =  $_POST['email'];
        $pass =  $_POST['password'];
        $confirm =  $_POST['confirm'];
        $rol =  $_POST['rol'];
        $estado =  $_POST['estado'];



        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            echo  "Email invalido";
        }elseif($pass !== $confirm){
            echo "Contraseñas no coinciden ";
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
                    echo "Usuario actualizado correctamente";
                 }elseif($stmt->sqlstate > 0){
                    echo "Advertencia, usuario actualizado con el código de advertencia: " . $stmt->sqlstate;
                 }else{
                    echo "Error, usuario no actualizado, código de error: " . $stmt->sqlstate;

                 }
                 $stmt->close();
            }else{
                //CREATE->INSERT de un usuario
                $sql = 'INSERT INTO usuarios (nombre, usuario, clave, correo, rol, estado) VALUES (?,?,?,?,?,?)';
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param('ssssss',$name,$usuario,$pass_hash,$email,$rol,$estado);
                $stmt->execute();
                if($stmt->sqlstate == '00000'){
                    echo "Usuario creado correctamente";
                }elseif($stmt->sqlstate > 0 ){
                    echo "Advertencia, usuario creado pero dio un mensaje: " . $stmt->sqlstate;
                }else{
                    echo "Error, el usuario no se pudo crear, código de error: " . $stmt->sqlstate;
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