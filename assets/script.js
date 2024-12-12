// assets/script.js

document.addEventListener('DOMContentLoaded', function() {
    // Función para confirmar eliminación
    window.confirmarEliminacion = function(id) {
        if (confirm('¿Está seguro que desea eliminar este producto?')) {
            window.location.href = 'eliminar.php?id=' + id;
        }
    }

    // Validación del formulario de compra
    const comprarForms = document.querySelectorAll('.comprar-form');
    comprarForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const cantidadInput = this.querySelector('input[name="cantidad_vendida"]');
            const cantidad = parseInt(cantidadInput.value);
            const max = parseInt(cantidadInput.max);

            if (cantidad > max) {
                e.preventDefault();
                alert('La cantidad solicitada excede el stock disponible');
            } else if (cantidad <= 0) {
                e.preventDefault();
                alert('Por favor ingrese una cantidad válida');
            }
        });
    });

    // Actualizar vista previa de imagen (si hay un input de imagen)
    const inputImagen = document.querySelector('input[type="file"]');
    if (inputImagen) {
        inputImagen.addEventListener('change', function(e) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.querySelector('#preview-imagen');
                if (preview) {
                    preview.src = e.target.result;
                }
            }
            reader.readAsDataURL(this.files[0]);
        });
    }

    // Mostrar mensajes temporales
    const mostrarMensaje = (mensaje, tipo = 'success') => {
        const div = document.createElement('div');
        div.className = `mensaje-${tipo}`;
        div.textContent = mensaje;
        document.body.appendChild(div);

        setTimeout(() => {
            div.remove();
        }, 3000);
    }

    // Si hay mensajes en la sesión, mostrarlos
    if (typeof mensajeSesion !== 'undefined') {
        mostrarMensaje(mensajeSesion);
    }
});
