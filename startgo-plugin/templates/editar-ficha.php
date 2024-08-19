<?php
// Verificar que el usuario esté logueado y tenga permisos de administrador
if (is_user_logged_in() && current_user_can('administrator')) {

    // Obtener el ID del post desde la URL
    $post_id = get_the_ID();

    // Manejar el envío del formulario
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Guardar los cambios del formulario en los campos personalizados
        update_field('nombre', $_POST['nombre'], $post_id);
        update_field('apellido', $_POST['apellido'], $post_id);
        update_field('email', $_POST['email'], $post_id);
        update_field('sugerencias', $_POST['sugerencias'], $post_id);
        update_field('pais', $_POST['pais'], $post_id);
        echo '<p>Los cambios han sido guardados.</p>';
    }

    // Obtener los datos actuales del post
    $nombre = get_field('nombre', $post_id);
    $apellido = get_field('apellido', $post_id);
    $email = get_field('email', $post_id);
    $sugerencias = get_field('sugerencias', $post_id);
    $pais = get_field('pais', $post_id);

    // Mostrar el formulario de edición
    ?>
    <h1><?php echo esc_html(get_the_title($post_id)); ?></h1>
    <form method="post">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" value="<?php echo esc_attr($nombre); ?>" /><br />
        <label for="apellido">Apellido:</label>
        <input type="text" name="apellido" value="<?php echo esc_attr($apellido); ?>" /><br />
        <label for="email">Correo Electrónico:</label>
        <input type="email" name="email" value="<?php echo esc_attr($email); ?>" /><br />
        <label for="sugerencias">Sugerencias:</label>
        <textarea name="sugerencias"><?php echo esc_textarea($sugerencias); ?></textarea><br />
        <label for="pais">País:</label>
        <input type="text" name="pais" value="<?php echo esc_attr($pais); ?>" /><br />
        <button type="submit">Guardar Cambios</button>
    </form>
    <?php
} else {
    wp_redirect(home_url());
    exit;
}
