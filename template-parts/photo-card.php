
<?php
/**
 * Carte photographique réutilisable.
 * Utilise le post courant de la boucle WordPress.
 *
 * @package Nathalie_Mota
 */
?>

<article class="photo-card">

    <?php
    $photo_id = get_the_ID();

    $reference = get_post_meta(
        $photo_id,
        '_photo_reference',
        true
    );

    $photo_categories = get_the_terms(
        $photo_id,
        'categorie'
    );

    $category_name = '';

    if (!is_wp_error($photo_categories) && !empty($photo_categories)) {
        $category_name = $photo_categories[0]->name;
    }
    ?>

    <?php
    if (has_post_thumbnail()) {
        the_post_thumbnail('large', array(
            'class'   => 'photo-card__image',
            'loading' => 'lazy',
        ));
    }
    ?>

    <div class="photo-card__overlay">

        <!-- Icône plein écran -->
        <button
            type="button"
            class="photo-card__fullscreen"
            data-photo-id="<?php echo esc_attr($photo_id); ?>"
            aria-label="<?php echo esc_attr('Afficher en plein écran : ' . get_the_title()); ?>"
        >
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <path
                    d="M8 3H3v5M16 3h5v5M3 16v5h5M21 16v5h-5"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
        </button>

        <!-- Accès à la page photo -->
        <a
            href="<?php the_permalink(); ?>"
            class="photo-card__eye"
            aria-label="<?php echo esc_attr('Voir la photographie : ' . get_the_title()); ?>"
        >
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <path
                    d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                />
                <circle
                    cx="12"
                    cy="12"
                    r="3"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                />
            </svg>
        </a>

        <!-- Informations -->
        <div class="photo-card__info">

            <span class="photo-card__reference">
                <?php echo esc_html($reference); ?>
            </span>

            <span class="photo-card__category">
                <?php echo esc_html($category_name); ?>
            </span>

        </div>

    </div>

</article>
