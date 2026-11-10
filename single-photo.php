
<?php
/**
 * Modèle d'une photographie.
 *
 * @package Nathalie_Mota
 */

get_header();

if (have_posts()) :
    while (have_posts()) :
        the_post();

        $photo_id = get_the_ID();


        
        // Navigation selon le numéro du fichier image.
        $photo_navigation = get_posts(array(
            'post_type'              => 'photo',
            'post_status'            => 'publish',
            'posts_per_page'         => -1,
            'fields'                 => 'ids',
            'no_found_rows'          => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ));

        $numbered_photos = array();

        foreach ($photo_navigation as $navigation_photo_id) {
            $thumbnail_id = get_post_thumbnail_id($navigation_photo_id);

            if (!$thumbnail_id) {
                continue;
            }

            $filename = get_attached_file($thumbnail_id);

            if (!$filename) {
                continue;
            }

            $basename = basename($filename);

            // Exemple : nathalie-14-scaled.jpeg
            if (preg_match('/^nathalie-(\d+)(?:-scaled)?\.(?:jpe?g|png|webp)$/i', $basename, $matches)) {
                $numbered_photos[$navigation_photo_id] = (int) $matches[1];
            }
        }

        // Classement numérique croissant.
        asort($numbered_photos, SORT_NUMERIC);

        $ordered_photo_ids = array_keys($numbered_photos);

        $current_position = array_search(
            $photo_id,
            $ordered_photo_ids,
            true
        );

        
        $previous_photo_id = null;
        $next_photo_id = null;

        $total_photos = count($ordered_photo_ids);

        if ($current_position !== false && $total_photos > 1) {

            // Navigation circulaire vers la photographie précédente.
            $previous_position = ($current_position - 1 + $total_photos) % $total_photos;

            // Navigation circulaire vers la photographie suivante.
            $next_position = ($current_position + 1) % $total_photos;

            $previous_photo_id = $ordered_photo_ids[$previous_position];
            $next_photo_id = $ordered_photo_ids[$next_position];
        }


        $type = get_post_meta(
            $photo_id,
            '_photo_type',
            true
        );

        $photo_date = get_post_meta(
            $photo_id,
            '_photo_date',
            true
        );

        $categories = get_the_terms($photo_id, 'categorie');
        $formats = get_the_terms($photo_id, 'format');

        $category_name = (
            !is_wp_error($categories) && !empty($categories)
        ) ? $categories[0]->name : '';

        $format_name = (
            !is_wp_error($formats) && !empty($formats)
        ) ? $formats[0]->name : '';

        $year = '';

        if (!empty($photo_date)) {
            $timestamp = strtotime($photo_date);

            if ($timestamp !== false) {
                $year = date('Y', $timestamp);
            }
        }

        
        // Récupération des photographies de la même catégorie.
        $related_photos = null;

        if (!is_wp_error($categories) && !empty($categories)) {

            $category_ids = wp_list_pluck($categories, 'term_id');

            $related_photos = new WP_Query(array(
                'post_type'      => 'photo',
                'posts_per_page' => 2,
                'post__not_in'   => array($photo_id),
                'orderby'        => 'rand',
                'tax_query'      => array(
                    array(
                        'taxonomy'         => 'categorie',
                        'field'            => 'term_id',
                        'terms'            => $category_ids,
                        'include_children' => false,
                    ),
                ),
            ));
        }

        ?>

        

        <main id="main-content" class="single-photo">

            <section class="single-photo__main">

                <div class="single-photo__image-container">
                    <?php
                    if (has_post_thumbnail()) {
                        the_post_thumbnail(
                            'full',
                            array(
                                'class' => 'single-photo__image',
                                'loading' => 'eager',
                                'fetchpriority' => 'high'
                            )
                        );
                    }
                    ?>
                </div>

                <div class="single-photo__details">

                    <h1 class="single-photo__title">
                        <?php the_title(); ?>
                    </h1>

                    <div class="single-photo__metadata">

                        <p>
                            Référence :
                            <?php echo esc_html($reference); ?>
                        </p>

                        <p>
                            Catégorie :
                            <?php echo esc_html($category_name); ?>
                        </p>

                        <p>
                            Format :
                            <?php echo esc_html($format_name); ?>
                        </p>

                        <p>
                            Type :
                            <?php echo esc_html($type); ?>
                        </p>

                        <p>
                            Année :
                            <?php echo esc_html($year); ?>
                        </p>

                    </div>

                </div>

            </section>

            
            <section class="single-photo__contact">

                <div class="single-photo__contact-content">

                    <p class="single-photo__contact-text">
                        Cette photo vous intéresse ?
                    </p>

                    <button
                        type="button"
                        class="single-photo__contact-button"
                        data-photo-reference="<?php echo esc_attr($reference); ?>"
                    >
                        Contact
                    </button>

                </div>
                                
                <nav
                    class="single-photo__navigation"
                    aria-label="Navigation entre les photographies"
                >
                    <?php
                    // Afficher la photographie précédente par défaut.
                    // Si elle n'existe pas, afficher la suivante.
                    $default_photo_id = $next_photo_id ?: $previous_photo_id;
                    ?>

                    <div class="single-photo__navigation-preview">
                        <?php if ($default_photo_id) : ?>
                            <?php
                            echo get_the_post_thumbnail(
                                $default_photo_id,
                                'thumbnail',
                                array(
                                    'class'   => 'single-photo__navigation-preview-image',
                                    'alt'     => '',
                                    'loading' => 'lazy',
                                )
                            );
                            ?>
                        <?php endif; ?>
                    </div>

                    <div class="single-photo__navigation-arrows">

                        <?php if ($previous_photo_id) : ?>
                            <a
                                class="single-photo__navigation-link single-photo__navigation-link--previous"
                                href="<?php echo esc_url(get_permalink($previous_photo_id)); ?>"
                                aria-label="Photographie précédente"
                            >
                                <span aria-hidden="true"></span>
                            </a>
                        <?php else : ?>
                            <span
                                class="single-photo__navigation-link single-photo__navigation-link--previous single-photo__navigation-link--disabled"
                                aria-hidden="true"
                            >
                                <span></span>
                            </span>
                        <?php endif; ?>

                        <?php if ($next_photo_id) : ?>
                            <a
                                class="single-photo__navigation-link single-photo__navigation-link--next"
                                href="<?php echo esc_url(get_permalink($next_photo_id)); ?>"
                                aria-label="Photographie suivante"
                            >
                                <span aria-hidden="true"></span>
                            </a>
                        <?php else : ?>
                            <span
                                class="single-photo__navigation-link single-photo__navigation-link--next single-photo__navigation-link--disabled"
                                aria-hidden="true"
                            >
                                <span></span>
                            </span>
                        <?php endif; ?>

                    </div>
                </nav>

            </section>

            <?php if ($related_photos && $related_photos->have_posts()) : ?>

                <section class="single-photo__related">

                    <h2 class="single-photo__related-title">
                        VOUS AIMEREZ AUSSI
                    </h2>

                    <div class="single-photo__related-grid">

                        <?php while ($related_photos->have_posts()) : $related_photos->the_post(); ?>

                            <?php get_template_part('template-parts/photo-card'); ?>

                        <?php endwhile; ?>

                        <?php wp_reset_postdata(); ?>

                    </div>

                </section>

            <?php endif; ?>


        </main>

        <?php get_template_part('template-parts/photo-lightbox'); ?>
        
        <?php
    endwhile;
endif;

get_footer();
