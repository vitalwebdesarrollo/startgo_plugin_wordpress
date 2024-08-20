jQuery(document).ready(function ($) {
    // Autocompletar los campos si el usuario está logueado
    if (startgo_ajax.user_data) {
        $('input[name="nombre"]').val(startgo_ajax.user_data.nombre);
        $('input[name="apellido"]').val(startgo_ajax.user_data.apellido);
        $('input[name="email"]').val(startgo_ajax.user_data.email);
    }

    // Llamada AJAX para obtener la lista de países
    $.ajax({
        url: 'https://restcountries.com/v3.1/all',
        method: 'GET',
        success: function (data) {
            data.sort(function (a, b) {
                return a.name.common.localeCompare(b.name.common);
            });

            var selectPais = $('#pais');
            selectPais.empty();

            data.forEach(function (pais) {
                selectPais.append('<option value="' + pais.name.common + '">' + pais.name.common + '</option>');
            });

            // Inicializar Select2 después de cargar los datos
            selectPais.select2({
                placeholder: "Seleccione un país",
                allowClear: true,
                width: '100%' // Ajustar el ancho del select
            });
        }
    });

    // Manejo del evento de envío del formulario
    $('form').on('submit', function (e) {
        e.preventDefault(); // Previene el comportamiento por defecto del formulario
        var datosFormulario = $(this).serialize();
        var esEdicion = !!$('input[name="post_id"]').val(); // Verificar si es una edición

        $.ajax({
            url: startgo_ajax.ajax_url,
            method: 'POST',
            data: {
                action: 'submit_formulario',
                nonce: startgo_ajax.nonce,
                datosFormulario: datosFormulario
            },
            success: function (response) {
                // Determinar el mensaje a mostrar
                var mensaje = esEdicion ? '¡La sugerencia ha sido actualizada correctamente!' : '¡Gracias por su sugerencia!';
                mostrarAlerta(mensaje);
            },
            error: function () {
                mostrarAlerta('Hubo un error al enviar el formulario.', 'danger');
            }
        });
    });

    // Función para mostrar un mensaje de alerta de Bootstrap
    function mostrarAlerta(mensaje, tipo = 'success') {
        var alerta = $(
            '<div class="alert alert-' + tipo + ' alert-dismissible fade show" role="alert">' +
            mensaje +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
            '<span aria-hidden="true">&times;</span>' +
            '</button>' +
            '</div>'
        );

        $('#alert-container').html(alerta);

        // Ocultar automáticamente después de 5 segundos
        setTimeout(function() {
            alerta.alert('close');
        }, 5000);
    }
});