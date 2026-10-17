
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
        'https://fonts.googleapis.com/css2?family=Poppins:wght@300&family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap',
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

    // Styles de la page d'accueil
    if (is_front_page()) {
        wp_enqueue_style(
            'nathalie-mota-home',
            get_template_directory_uri() . '/assets/css/home.css',
            array('nathalie-mota-global'),
            filemtime(get_template_directory() . '/assets/css/home.css')
    );
    }
}
/**
 * Chargement du type de contenu Photos.
 */
require_once get_template_directory() . '/inc/photo-post-type.php';

add_action('wp_enqueue_scripts', 'nathalie_mota_enqueue_styles');
