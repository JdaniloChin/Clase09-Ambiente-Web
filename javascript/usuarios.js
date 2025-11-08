document.addEventListener('DOMContentLoaded', function() {
    const btnsEditar = document.querySelectorAll('.btnEditar');
    const modalElement = document.getElementById('modalFormulario');
    const modal = new bootstrap.Modal(modalElement);
    const modalTitle = document.getElementById('modalFormularioLabel');
    const form = document.getElementById('formularioUsuarios');
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('confirm');

    cargarUsuarios();

    btnsEditar.forEach(btn => {
        btn.addEventListener('click', function (){
            const id = this.getAttribute('data-id');
            const nombre = this.getAttribute('data-nombre');
            const usuario = this.getAttribute('data-usuario');
            const correo = this.getAttribute('data-correo');
            const rol = this.getAttribute('data-rol');
            const estado = this.getAttribute('data-estado');

            //cambiar titulo del modal
            modalTitle.textContent = 'Editar Usuario';

            //precargar los datos del usuario en la tabla al formulario
            document.getElementById('usuario_id').value = id;
            document.getElementById('nombre').value = nombre;
            document.getElementById('usuario').value = usuario;
            document.getElementById('email').value = correo;
            document.getElementById('rol').value = rol;
            document.getElementById('estado').value = estado;

            //Contraseña por seguridad se queda en blanco
            document.getElementById('password').value='';
            document.getElementById('confirm').value= '';

            //Quitar required para edicion
            passwordInput.removeAttribute('required');
            confirmInput.removeAttribute('required');

            modal.show();

        })
    });

    function cargarUsuarios(){
        console.log("cargarUsuarios");
        $.get('/api/obtener_usuarios.php', function(data){
            $('#tablaUsuarios tbody').html(data);
        });
    }

})