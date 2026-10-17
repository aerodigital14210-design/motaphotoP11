<?php
/**
 * Champs personnalisés des photographies.
 *
 * @package Nathalie_Mota
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Ajouter l'encadré dans l'éditeur des photos.
 */
function nathalie_mota_add_photo_meta_boxes() {
    add_meta_box(
        'nathalie_mota_photo_details',
        'Informations de la photographie',
        'nathalie_mota_render_photo_meta_box',
        'photo',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'nathalie_mota_add_photo_meta_boxes');

/**
 * Afficher les champs.
 */
function nathalie_mota_render_photo_meta_box($post) {

    wp_nonce_field(
        'nathalie_mota_save_photo_details',
        'nathalie_mota_photo_nonce'
    );

    $reference = get_post_meta($post->ID, '_photo_reference', true);
    $type      = get_post_meta($post->ID, '_photo_type', true);
    $date      = get_post_meta($post->ID, '_photo_date', true);
    ?>

    <p>
        <label for="photo_reference">
            <strong>Référence photo</strong>
        </label>
    </p>
    <p>
        <input
            type="text"
            id="photo_reference"
            name="photo_reference"
            value="<?php echo esc_attr($reference); ?>"
            class="widefat"
            placeholder="Ex. : BF2400"
        >
    </p>

    <p>
        <label for="photo_type">
            <strong>Type de photographie</strong>
        </label>
    </p>
    <p>
        <input
            type="text"
            id="photo_type"
            name="photo_type"
            value="<?php echo esc_attr($type); ?>"
            class="widefat"
            placeholder="Ex. : Numérique"
        >
    </p>

    <p>
        <label for="photo_date">
            <strong>Date de prise de vue</strong>
        </label>
    </p>
    <p>
        <input
            type="date"
            id="photo_date"
            name="photo_date"
            value="<?php echo esc_attr($date); ?>"
        >
    </p>

    <?php
}

/**
 * Enregistrer les informations.
 */
function nathalie_mota_save_photo_details($post_id) {

    if (
        !isset($_POST['nathalie_mota_photo_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['nathalie_mota_photo_nonce'])),
            'nathalie_mota_save_photo_details'
        )
    ) {
        return;
    }

    if (
        defined('DOING_AUTOSAVE') &&
        DOING_AUTOSAVE
    ) {
        return;
    }

    if (
        get_post_type($post_id) !== 'photo' ||
        !current_user_can('edit_post', $post_id)
    ) {
        return;
    }

    $fields = array(
        'photo_reference' => '_photo_reference',
        'photo_type'      => '_photo_type',
        'photo_date'      => '_photo_date',
    );

    foreach ($fields as $field => $meta_key) {

        if (!isset($_POST[$field])) {
            continue;
        }

        $value = sanitize_text_field(
            wp_unslash($_POST[$field])
        );

        if ($field === 'photo_date') {
            $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);

            if (
                $value !== '' &&
                (!$date || $date->format('Y-m-d') !== $value)
            ) {
                continue;
            }
        }

        if ($value === '') {
            delete_post_meta($post_id, $meta_key);
        } else {
            update_post_meta($post_id, $meta_key, $value);
        }
    }
}
add_action('save_post_photo', 'nathalie_mota_save_photo_details');