<?php
?>
<aside class="col-md-3 bg-dark text-white p-4">
                <h4 class="mb-4">Menú</h4>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link text-white" href="./home.php">Inicio</a></li>
                    <?php if(isset($_SESSION['rol']) && $_SESSION['rol'] === "Admin"){
                            echo '<li class="nav-item"><a class="nav-link text-white" href="./usuarios.php">Usuarios</a></li>';
                        }
                         ?>
                    <li class="nav-item"><a class="nav-link text-white" href="#">Productos</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="#">Ventas</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="#">Reportes</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="#">Configuración</a></li>
                </ul>
                <form action="includes/logout.php" method="post" class="mt-4">
                    <button type="submit" class="btn btn-danger w-100">Cerrar Sesión</button>
                </form>
</aside>