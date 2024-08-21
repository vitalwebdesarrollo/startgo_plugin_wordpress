<?php
/**
 * Plugin Name: StartGo Plugin
 * Description: Plugin desarrollado para la prueba técnica de StartGo.
 * Version: 2.0
 * Author: Edgar Paez
 * Text Domain: startgo-plugin
 * Domain Path: /languages
 */

 //Función para la carga de idioma
 function startgo_plugin_cargar_textdomain() {
    load_plugin_textdomain('startgo-plugin', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

add_action('plugins_loaded', 'startgo_plugin_cargar_textdomain');


//carga de assets del bloque de gutemberg
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

function enqueue_custom_styles() {
    wp_enqueue_style(
        'custom-style', // Handle
        get_template_directory_uri() . '/css/style.css', // Ruta al archivo CSS
        array(), // Dependencias
        '1.0', // Versión
        'all' // Medios
    );
}
add_action('wp_enqueue_scripts', 'enqueue_custom_styles');

add_action('enqueue_block_editor_assets', 'startgo_plugin_cargar_assets');

function enqueue_select2() {
    // Registrar e incluir el script de Select2
    wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', array('jquery'), '4.0.13', true);

    // Registrar e incluir el estilo de Select2
    wp_enqueue_style('select2-css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css');
}
add_action('wp_enqueue_scripts', 'enqueue_select2');

function startgo_plugin_cargar_scripts_frontend() {
    wp_enqueue_script(
        'startgo-frontend',
        plugins_url('js/frontend.js', __FILE__),
        array('jquery'),
        null,
        true
    );

    // Incluir Bootstrap CSS
    wp_enqueue_style(
        'bootstrap-css',
        'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css'
    );

     // Incluir Bootstrap JS 
     wp_enqueue_script(
        'bootstrap-js',
        'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js',
        array('jquery'),
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
        'user_data' => is_user_logged_in() ? $user_data : null
    ));
}

add_action('wp_enqueue_scripts', 'startgo_plugin_cargar_scripts_frontend');

function startgo_plugin_manejar_envio_formulario() {
    check_ajax_referer('startgo_nonce', 'nonce');

    $datos = [];
    parse_str($_POST['datosFormulario'], $datos);

    $post_id = isset($datos['post_id']) ? intval($datos['post_id']) : 0;

    if ($post_id > 0) {
        // Actualizar post existente
        $updated_post = array(
            'ID'           => $post_id,
            'post_title'   => sanitize_text_field($datos['nombre'] . ' ' . $datos['apellido']),
        );
        
        wp_update_post($updated_post);

        // Actualizar los campos personalizados
        update_field('nombre', $datos['nombre'], $post_id);
        update_field('apellido', $datos['apellido'], $post_id);
        update_field('email', $datos['email'], $post_id);
        update_field('sugerencias', $datos['sugerencias'], $post_id);
        update_field('pais', $datos['pais'], $post_id);

        wp_send_json_success('Sugerencia actualizada correctamente');
    } else {
        // Crear un nuevo Custom Post Type con los datos del formulario
        $post_id = wp_insert_post(array(
            'post_title' => sanitize_text_field($datos['nombre'] . ' ' . $datos['apellido']),
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
}


add_action('wp_ajax_submit_formulario', 'startgo_plugin_manejar_envio_formulario');
add_action('wp_ajax_nopriv_submit_formulario', 'startgo_plugin_manejar_envio_formulario');

function startgo_plugin_crear_post_type_sugerencias() {
    $labels = array(
        'name' => __('Sugerencias', 'startgo-plugin'),
        'singular_name' => __('Sugerencia', 'startgo-plugin'),
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'menu_position' => 20,
        'supports' => array('title')
    );

    register_post_type('sugerencias', $args);
}

add_action('init', 'startgo_plugin_crear_post_type_sugerencias');

function startgo_plugin_shortcode_sugerencias() {
    if (!current_user_can('administrator')) {
        return '<div class="alert alert-primary" role="alert">' . __('No tienes permiso para ver este contenido.', 'startgo-plugin') . '</div>';
    }

    if (isset($_GET['updated']) && $_GET['updated'] == 'true') {
        echo '<div class="alert alert-success">' . __('La sugerencia se ha actualizado correctamente.', 'startgo-plugin') . '</div>';
    }

    // Obtener la página actual
    $paged = get_query_var('paged') ? get_query_var('paged') : 1;

    // Ajustar la consulta para la paginación
    $args = array(
        'post_type' => 'sugerencias',
        'posts_per_page' => 10, // Número de posts por página
        'paged' => $paged // Página actual
    );

    $query = new WP_Query($args);
    if ($query->have_posts()) {
        $output = '<div class="table-responsive-sm">';
        $output .= '<table class="table table-bordered">';
        $output .= '<thead class="thead-dark"><tr>';
        $output .= '<th>' . __('Nombre', 'startgo-plugin') . '</th>';
        $output .= '<th>' . __('Apellido', 'startgo-plugin') . '</th>';
        $output .= '<th>' . __('Email', 'startgo-plugin') . '</th>';
        $output .= '<th>' . __('Sugerencias', 'startgo-plugin') . '</th>';
        $output .= '<th>' . __('Acciones', 'startgo-plugin') . '</th>';
        $output .= '</tr></thead><tbody>';

        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $output .= '<tr>';
            $output .= '<td>' . esc_html(get_field('nombre')) . '</td>';
            $output .= '<td>' . esc_html(get_field('apellido')) . '</td>';
            $output .= '<td>' . esc_html(get_field('email')) . '</td>';
            $output .= '<td>' . esc_html(get_field('sugerencias')) . '</td>';
            $output .= '<td><a href="' . site_url() . '/' . $post_id . '/editar-ficha/" class="btn btn-primary">' . __('Editar', 'startgo-plugin') . '</a></td>';
            $output .= '</tr>';
        }
        $output .= '</tbody></table>';
        $output .= '</div>';

        $big = 999999999;
        $pagination_links = paginate_links(array(
            'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
            'format' => '?paged=%#%',
            'current' => max(1, get_query_var('paged')),
            'total' => $query->max_num_pages,
            'prev_text' => __('Previous', 'startgo-plugin'),
            'next_text' => __('Next', 'startgo-plugin'),
            'type' => 'array',
        ));

        if ($pagination_links) {
            $output .= '<div class="pagination-container">';
            $output .= '<nav aria-label="Page navigation">';
            $output .= '<ul class="pagination">';

            foreach ($pagination_links as $link) {
                $output .= '<li class="page-item">' . $link . '</li>';
            }

            $output .= '</ul></nav>';
            $output .= '</div>';
        }

        wp_reset_postdata();
    } else {
        $output = '<div class="alert alert-danger" role="alert">' . __('No se encontraron sugerencias.', 'startgo-plugin') . '</div>';
    }

    return $output;
}

add_shortcode('mostrar_sugerencias', 'startgo_plugin_shortcode_sugerencias');




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

