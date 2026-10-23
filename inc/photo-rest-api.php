
<?php
/**
 * API REST du catalogue photographique.
 *
 * @package Nathalie_Mota
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enregistre la route REST des photographies.
 */

function nathalie_mota_register_photo_routes() {

    register_rest_route('nathalie-mota/v1', '/photos', array(

        'methods'             => WP_REST_Server::READABLE,
        'callback'            => 'nathalie_mota_get_filtered_photos',
        'permission_callback' => '__return_true',

        'args' => array(

            'categorie' => array(
                'type'              => 'string',
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
            ),

            'format' => array(
                'type'              => 'string',
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
            ),

            'tri' => array(
                'type'    => 'string',
                'default' => 'DESC',
                'enum'    => array('ASC', 'DESC'),
            ),

            'page' => array(
                'type'    => 'integer',
                'default' => 1,
                'minimum' => 1,
            ),

        ),
    ));
}

add_action('rest_api_init', 'nathalie_mota_register_photo_routes');

/**
 * Renvoie les photographies filtrées.
 */
function nathalie_mota_get_filtered_photos(WP_REST_Request $request) {

    $categorie = sanitize_title((string) $request->get_param('categorie'));
    $photo_format = sanitize_title((string) $request->get_param('format'));
    $tri = strtoupper((string) $request->get_param('tri'));
    $page = max(1, absint($request->get_param('page')));

    $tax_query = array();

    if (!empty($categorie)) {
        $tax_query[] = array(
            'taxonomy' => 'categorie',
            'field'    => 'slug',
            'terms'    => $categorie,
        );
    }

    if (!empty($photo_format)) {
        $tax_query[] = array(
            'taxonomy' => 'format',
            'field'    => 'slug',
            'terms'    => $photo_format,
        );
    }

    if (count($tax_query) > 1) {
        $tax_query['relation'] = 'AND';
    }

    $args = array(
        'post_type'      => 'photo',
        'post_status'    => 'publish',
        'posts_per_page' => 8,
        'paged'          => $page,
        'orderby'        => 'date',
        'order'          => $tri,
    );

    if (!empty($tax_query)) {
        $args['tax_query'] = $tax_query;
    }

    $query = new WP_Query($args);

    $photos = array();

    while ($query->have_posts()) {
        $query->the_post();

        $photo_id = get_the_ID();

        $photos[] = array(
            'id'        => $photo_id,
            'title'     => get_the_title(),
            'permalink' => get_permalink(),
            'image'     => get_the_post_thumbnail_url($photo_id, 'large'),
            'alt'       => get_post_meta(
                get_post_thumbnail_id($photo_id),
                '_wp_attachment_image_alt',
                true
            ),
        );
    }

    wp_reset_postdata();

    return rest_ensure_response(array(
        'photos'       => $photos,
        'total'        => (int) $query->found_posts,
        'total_pages'  => (int) $query->max_num_pages,
        'current_page' => $page,
    ));
}
