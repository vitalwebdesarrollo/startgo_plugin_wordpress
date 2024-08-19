<?php
/**
 * Plugin Name: StartGo Plugin
 * Description: Plugin desarrollado para la prueba técnica de StartGo.
 * Version: 1.0
 * Author: Edgar Paez
 * Text Domain: startgo-plugin
 * Domain Path: /languages
 */

 function startgo_plugin_cargar_assets() {
    wp_enqueue_script(
        'startgo-plugin-bloque',
        plugins_url('bloque/build/index.js', __FILE__),
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-data', 'wp-i18n')
    );

    wp_enqueue_style(
        'startgo-plugin-editor-estilo',
        plugins_url('bloque/build/index.css', __FILE__),
        array('wp-edit-blocks')
    );
}

add_action('enqueue_block_editor_assets', 'startgo_plugin_cargar_assets');

function startgo_plugin_cargar_scripts_frontend() {
    wp_enqueue_script(
        'startgo-frontend',
        plugins_url('js/frontend.js', __FILE__),
        array('jquery'), // Dependencias
        null,
        true
    );

    // Obtenemos la información del usuario logueado
    $current_user = wp_get_current_user();
    $user_data = array(
        'nombre' => $current_user->user_firstname,
        'apellido' => $current_user->user_lastname,
        'email' => $current_user->user_email,
    );

    // Pasamos la información del usuario y AJAX al script de frontend
    wp_localize_script('startgo-frontend', 'startgo_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('startgo_nonce'),
        'user_data' => is_user_logged_in() ? array(
            'nombre' => wp_get_current_user()->first_name,
            'apellido' => wp_get_current_user()->last_name,
            'email' => wp_get_current_user()->user_email
        ) : null
    ));
}

add_action('wp_enqueue_scripts', 'startgo_plugin_cargar_scripts_frontend');



function startgo_plugin_manejar_envio_formulario() {
    check_ajax_referer('startgo-nonce', 'nonce');

    $datos = [];
    parse_str($_POST['datosFormulario'], $datos);

    // Crear un nuevo Custom Post Type con los datos del formulario
    $post_id = wp_insert_post(array(
        'post_title' => $datos['nombre'] . ' ' . $datos['apellido'],
        'post_type' => 'sugerencias',
        'post_status' => 'publish'
    ));

    if ($post_id) {
        // Guardar los campos personalizados utilizando ACF
        update_field('nombre', $datos['nombre'], $post_id);
        update_field('apellido', $datos['apellido'], $post_id);
        update_field('email', $datos['email'], $post_id);
        update_field('sugerencias', $datos['sugerencias'], $post_id);
        update_field('pais', $datos['pais'], $post_id);
    }

    wp_send_json_success('Formulario enviado correctamente');
}

add_action('wp_ajax_submit_formulario', 'startgo_plugin_manejar_envio_formulario');
add_action('wp_ajax_nopriv_submit_formulario', 'startgo_plugin_manejar_envio_formulario');

function startgo_plugin_crear_post_type() {
    register_post_type('sugerencias', array(
        'labels' => array(
            'name' => __('Sugerencias', 'startgo-plugin'),
            'singular_name' => __('Sugerencia', 'startgo-plugin'),
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor'),
    ));
}

add_action('init', 'startgo_plugin_crear_post_type');


function startgo_plugin_shortcode_sugerencias() {
    if (!current_user_can('administrator')) {
        return 'No tienes permiso para ver este contenido.';
    }

    $output = '<table class="table table-striped">';
    $output .= '<thead><tr><th>Nombre</th><th>Correo Electrónico</th><th>Sugerencias</th><th>Editar</th></tr></thead><tbody>';

    $consulta = new WP_Query(array(
        'post_type' => 'sugerencias',
        'posts_per_page' => -1,
    ));

    if ($consulta->have_posts()) {
        while ($consulta->have_posts()) {
            $consulta->the_post();
            $post_id = get_the_ID();
            $nombre = get_field('nombre', $post_id);
            $email = get_field('email', $post_id);
            $sugerencias = get_field('sugerencias', $post_id);

            $output .= '<tr>';
            $output .= '<td>' . esc_html($nombre) . '</td>';
            $output .= '<td>' . esc_html($email) . '</td>';
            $output .= '<td>' . esc_html($sugerencias) . '</td>';
            $output .= '<td><a href="' . site_url() . '/' . $post_id . '/editar-ficha/">Editar</a></td>';
            $output .= '</tr>';
        }
    }

    wp_reset_postdata();
    $output .= '</tbody></table>';

    return $output;
}

add_shortcode('tabla_sugerencias', 'startgo_plugin_shortcode_sugerencias');

function startgo_plugin_redirigir_editar_ficha() {
    if (is_admin()) {
        return;
    }

    // Verifica si la URL sigue el formato "/id/editar-ficha/"
    $uri = $_SERVER['REQUEST_URI'];
    if (preg_match('/\/(\d+)\/editar-ficha\/$/', $uri, $matches)) {
        $post_id = $matches[1];
        if (get_post_type($post_id) === 'sugerencias') {
            // Cargar la plantilla de edición desde el plugin
            include plugin_dir_path(__FILE__) . 'templates/editar-ficha.php';
            exit;
        }
    }
}

add_action('template_redirect', 'startgo_plugin_redirigir_editar_ficha');
