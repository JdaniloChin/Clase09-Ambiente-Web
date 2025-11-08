
(function () {
      const toggle = document.getElementById('togglePassword');
      const input  = document.getElementById('password');

      toggle.addEventListener('click', function () {
        // preservar posición del cursor
        const start = input.selectionStart;
        const end   = input.selectionEnd;

        const mostrar = input.type === 'password';
        input.type = mostrar ? 'text' : 'password';

        // alternar icono (bi-eye <-> bi-eye-slash)
        const icon = this.querySelector('i');
        icon.classList.toggle('bi-eye', !mostrar);         // muestra ojo cuando oculto
        icon.classList.toggle('bi-eye-slash', mostrar);    // muestra ojo tachado cuando visible

        // actualizar aria-label para accesibilidad
        this.setAttribute('aria-label', mostrar ? 'Ocultar contraseña' : 'Mostrar contraseña');
        this.setAttribute('title', this.getAttribute('aria-label'));

        // restaurar selección y foco (small timeout para asegurar que el cambio de type se aplique)
        setTimeout(() => {
          input.focus();
          if (start !== null && end !== null) input.setSelectionRange(start, end);
        }, 0);
      });
    })();