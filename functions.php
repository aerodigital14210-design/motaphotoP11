
<?php
/**
 * Fonctions du thème Nathalie Mota.
 *
 * @package Nathalie_Mota
 */

if (!defined('ABSPATH')) {
    exit;
}

function nathalie_mota_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script'
    ));

    register_nav_menus(array(
        'primary' => 'Menu principal',
        'footer'  => 'Menu pied de page'
    ));
}
add_action('after_setup_theme', 'nathalie_mota_setup');


function nathalie_mota_enqueue_styles() {

    $version = wp_get_theme()->get('Version');

    // Polices Space Mono et Poppins
    wp_enqueue_style(
        'nathalie-mota-fonts',
        'https://fonts.googleapis.com/css2?family=Poppins:wght@300;500&family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap',
        array(),
        null
    );

    // Déclaration du thème
    wp_enqueue_style(
        'nathalie-mota-style',
        get_stylesheet_uri(),
        array(),
        $version
    );

    // Styles globaux
    wp_enqueue_style(
        'nathalie-mota-global',
        get_template_directory_uri() . '/assets/css/global.css',
        array('nathalie-mota-style', 'nathalie-mota-fonts'),
        $version
    );

    // Header
    wp_enqueue_style(
        'nathalie-mota-header',
        get_template_directory_uri() . '/assets/css/header.css',
        array('nathalie-mota-global'),
        $version
    );

    // Menu mobile
    wp_enqueue_script(
        'nathalie-mota-menu',
        get_template_directory_uri() . '/assets/js/menu.js',
        array(),
        filemtime(get_template_directory() . '/assets/js/menu.js'),
        true
    );

    // Footer
    wp_enqueue_style(
        'nathalie-mota-footer',
        get_template_directory_uri() . '/assets/css/footer.css',
        array('nathalie-mota-global'),
        filemtime(get_template_directory() . '/assets/css/footer.css')
    );

    // Styles de la modale de contact
    wp_enqueue_style(
        'nathalie-mota-contact-modal',
        get_template_directory_uri() . '/assets/css/contact-modal.css',
        array('nathalie-mota-global'),
        filemtime(get_template_directory() . '/assets/css/contact-modal.css')
    );
    
    // Gestion de la modale de contact
        wp_enqueue_script(
            'nathalie-mota-contact-modal',
            get_template_directory_uri() . '/assets/js/contact-modal.js',
            array(),
            filemtime(get_template_directory() . '/assets/js/contact-modal.js'),
            true
        );

    // Styles de la page d'accueil
    if (is_front_page()) {
        wp_enqueue_style(
            'nathalie-mota-home',
            get_template_directory_uri() . '/assets/css/home.css',
            array('nathalie-mota-global'),
            filemtime(get_template_directory() . '/assets/css/home.css')
        );
        wp_enqueue_script(
            'nathalie-mota-photo-filters',
            get_template_directory_uri() . '/assets/js/photo-filters.js',
            array(),
            filemtime(get_template_directory() . '/assets/js/photo-filters.js'),
            true
        );
        wp_enqueue_script(
            'nathalie-mota-photo-gallery',
            get_template_directory_uri() . '/assets/js/photo-gallery.js',
            array('nathalie-mota-photo-filters'),
            filemtime(get_template_directory() . '/assets/js/photo-gallery.js'),
            true
        );

    }

    
    // Lightbox : accueil et fiches photographiques
    if (is_front_page() || is_singular('photo')) {

        wp_enqueue_style(
            'nathalie-mota-lightbox',
            get_template_directory_uri() . '/assets/css/lightbox.css',
            array('nathalie-mota-global'),
            filemtime(get_template_directory() . '/assets/css/lightbox.css')
        );

        wp_enqueue_script(
            'nathalie-mota-lightbox',
            get_template_directory_uri() . '/assets/js/lightbox.js',
            array(),
            filemtime(get_template_directory() . '/assets/js/lightbox.js'),
            true
        );
        
        wp_enqueue_script(
            'nathalie-mota-lightbox',
            get_template_directory_uri() . '/assets/js/lightbox.js',
            array(),
            filemtime(get_template_directory() . '/assets/js/lightbox.js'),
            true
        );

        // URL de l'API REST pour la lightbox
        wp_add_inline_script(
            'nathalie-mota-lightbox',
            'window.nathalieMotaGallery = ' . wp_json_encode(array(
                'apiUrl' => esc_url_raw(
                    rest_url('nathalie-mota/v1/photos')
                ),
            )) . ';',
            'before'
        );

    }


    
    
// Styles de la page individuelle d'une photographie
    if (is_singular('photo')) {

        // Réutilisation des styles des cartes photographiques
        wp_enqueue_style(
            'nathalie-mota-home',
            get_template_directory_uri() . '/assets/css/home.css',
            array('nathalie-mota-global'),
            filemtime(
                get_template_directory() . '/assets/css/home.css'
            )
        );

        wp_enqueue_style(
            'nathalie-mota-single-photo',
            get_template_directory_uri() . '/assets/css/single-photo.css',
            array('nathalie-mota-home'),
            filemtime(
                get_template_directory() . '/assets/css/single-photo.css'
            )
        );
    }


}
/**
 * Chargement du type de contenu Photos.
 */
require_once get_template_directory() . '/inc/photo-post-type.php';

/**
 * Champs personnalisés des photographies.
 */
require_once get_template_directory() . '/inc/photo-meta-boxes.php';

/**
 * API REST du catalogue photographique.
 */
require_once get_template_directory() . '/inc/photo-rest-api.php';

add_action('wp_enqueue_scripts', 'nathalie_mota_enqueue_styles');
