jQuery(document).ready(function ($) {
    // Verificar si los datos del usuario están disponibles
    console.log(startgo_ajax.user_data);

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
            var selectPais = $('#pais');
            data.forEach(function (pais) {
                selectPais.append('<option value="' + pais.name.common + '">' + pais.name.common + '</option>');
            });
        }
    });

    // Manejo del evento de envío del formulario
    $('form').on('submit', function (e) {
        e.preventDefault(); // Previene el comportamiento por defecto del formulario
        var datosFormulario = $(this).serialize();

        $.ajax({
            url: startgo_ajax.ajax_url,
            method: 'POST',
            data: {
                action: 'submit_formulario',
                nonce: startgo_ajax.nonce,
                datosFormulario: datosFormulario
            },
            success: function (response) {
                alert('¡Gracias por su sugerencia!');
            }
        });
    });
});
