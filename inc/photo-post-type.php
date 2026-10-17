<?php
/**
 * Gestion des photographies.
 *
 * @package Nathalie_Mota
 */

if (!defined('ABSPATH')) {
    exit;
}

function nathalie_mota_register_photos() {

    register_post_type('photo', array(
        'labels' => array(
            'name'          => 'Photos',
            'singular_name' => 'Photo',
            'add_new_item'  => 'Ajouter une photo',
            'edit_item'     => 'Modifier la photo',
            'all_items'     => 'Toutes les photos',
        ),
        'public'       => true,
        'has_archive'  => true,
        'menu_icon'    => 'dashicons-camera',
        'show_in_rest' => true,
        'supports'     => array('title', 'thumbnail', 'editor'),
        'rewrite'      => array('slug' => 'photos'),
    ));

    register_taxonomy('categorie', 'photo', array(
        'labels' => array(
            'name'          => 'Catégories',
            'singular_name' => 'Catégorie',
        ),
        'public'            => true,
        'hierarchical'      => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => array('slug' => 'categorie-photo'),
    ));

    register_taxonomy('format', 'photo', array(
        'labels' => array(
            'name'          => 'Formats',
            'singular_name' => 'Format',
        ),
        'public'            => true,
        'hierarchical'      => false,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => array('slug' => 'format-photo'),
    ));
}

add_action('init', 'nathalie_mota_register_photos');