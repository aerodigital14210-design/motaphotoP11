
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
    wp_enqueue_style(
        'nathalie-mota-style',
        get_stylesheet_uri(),
        array(),
        wp_get_theme()->get('Version')
    );
}
add_action('wp_enqueue_scripts', 'nathalie_mota_enqueue_styles');
