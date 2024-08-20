<?php
// Verificar que el usuario esté logueado y tenga permisos de administrador
if (!is_user_logged_in() || !current_user_can('administrator')) {
    wp_redirect(home_url());
    exit;
}

// Obtener el ID del post desde la URL
$uri = $_SERVER['REQUEST_URI'];
if (preg_match('/\/(\d+)\/editar-ficha\/$/', $uri, $matches)) {
    $post_id = $matches[1];
} else {
    wp_die(__('ID de sugerencia no válido', 'startgo-plugin'));
}

// Verificar que el post existe y es del tipo correcto
$post = get_post($post_id);
if (!$post || get_post_type($post) !== 'sugerencias') {
    wp_die(__('Sugerencia no encontrada', 'startgo-plugin'));
}

// Manejar el envío del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Actualizar el post existente
    $updated_post = array(
        'ID'           => $post_id,
        'post_title'   => sanitize_text_field($_POST['nombre'] . ' ' . $_POST['apellido']),
        'post_content' => sanitize_textarea_field($_POST['sugerencias']),
    );
    
    // Actualizar el post
    wp_update_post($updated_post);

    // Actualizar los campos personalizados
    update_field('nombre', sanitize_text_field($_POST['nombre']), $post_id);
    update_field('apellido', sanitize_text_field($_POST['apellido']), $post_id);
    update_field('email', sanitize_email($_POST['email']), $post_id);
    update_field('sugerencias', sanitize_textarea_field($_POST['sugerencias']), $post_id);
    update_field('pais', sanitize_text_field($_POST['pais']), $post_id);

    echo '<div class="alert alert-success">' . __('La sugerencia ha sido actualizada correctamente.', 'startgo-plugin') . '</div>';
}

// Obtener los datos actuales del post
$nombre = get_field('nombre', $post_id);
$apellido = get_field('apellido', $post_id);
$email = get_field('email', $post_id);
$sugerencias = get_field('sugerencias', $post_id);
$pais = get_field('pais', $post_id);

// Mostrar el formulario de edición
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], input[type="email"], textarea { width: 100%; padding: 8px; }
        button { background-color: #4CAF50; color: white; padding: 10px 15px; border: none; cursor: pointer; }
        button:hover { background-color: #45a049; }
    </style>
</head>
<body>
    <h1><?php _e('Editar Sugerencia', 'startgo-plugin'); ?></h1>
    <form method="post">
        <div class="form-group">
            <input type="hidden" name="post_id" value="<?php echo esc_attr($post_id); ?>">
            <label for="nombre"><?php _e('Nombre:', 'startgo-plugin'); ?></label>
            <input type="text" id="nombre" name="nombre" value="<?php echo esc_attr($nombre); ?>" required>
        </div>
        <div class="form-group">
            <label for="apellido"><?php _e('Apellido:', 'startgo-plugin'); ?></label>
            <input type="text" id="apellido" name="apellido" value="<?php echo esc_attr($apellido); ?>" required>
        </div>
        <div class="form-group">
            <label for="email"><?php _e('Correo Electrónico:', 'startgo-plugin'); ?></label>
            <input type="email" id="email" name="email" value="<?php echo esc_attr($email); ?>" required>
        </div>
        <div class="form-group">
            <label for="sugerencias"><?php _e('Sugerencias:', 'startgo-plugin'); ?></label>
            <textarea id="sugerencias" name="sugerencias" rows="4" required><?php echo esc_textarea($sugerencias); ?></textarea>
        </div>
        <div class="form-group">
            <label for="pais"><?php _e('País:', 'startgo-plugin'); ?></label>
            <input type="text" id="pais" name="pais" value="<?php echo esc_attr($pais); ?>" required>
        </div>
        <div id="alert-container"></div>
        <button type="submit"><?php _e('Actualizar Sugerencia', 'startgo-plugin'); ?></button>
    </form>
    <?php wp_footer(); ?>
</body>
</html>
