
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

        $reference = get_post_meta(
            $photo_id,
            '_photo_reference',
            true
        );

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
