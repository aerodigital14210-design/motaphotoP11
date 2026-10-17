<?php
/**
 * Page d'accueil - Nathalie Mota.
 *
 * @package Nathalie_Mota
 */

get_header();
?>

<main id="main-content">

    <section class="home-hero" aria-labelledby="hero-title">

        <img
            class="home-hero__image"
            src="<?php echo esc_url(get_theme_file_uri('/assets/images/hero.png')); ?>"
            alt="Photographie d'un événement festif avec des invités"
            width="1440"
            height="962"
            fetchpriority="high"
        >

        <h1 id="hero-title" class="home-hero__title">
            Photographe Event
        </h1>

    </section>

    <!-- Galerie photographique -->
    <section class="photo-gallery" aria-label="Catalogue des photographies">
            
    
    <!-- Filtres du catalogue -->
    <div class="photo-filters" aria-label="Filtres des photographies">

        <div class="photo-filters__group">

            <!-- Catégories -->
            <div class="photo-filter photo-filter--category" data-filter="categorie">
                <button
                    type="button"
                    class="photo-filter__trigger"
                    id="photo-category"
                    aria-expanded="false"
                    aria-controls="photo-category-options"
                    aria-haspopup="true"
                >
                    <span class="photo-filter__label">CATÉGORIES</span>
                    <span class="photo-filter__chevron" aria-hidden="true"></span>
                </button>

                <div class="photo-filter__options" id="photo-category-options" hidden>
                    <button type="button" class="photo-filter__option" data-value="">
                        Toutes les catégories
                    </button>

                    <?php
                    $categories = get_terms(array(
                        'taxonomy'   => 'categorie',
                        'hide_empty' => false,
                    ));

                    if (!is_wp_error($categories)) :
                        foreach ($categories as $category) :
                    ?>
                        <button
                            type="button"
                            class="photo-filter__option"
                            data-value="<?php echo esc_attr($category->slug); ?>"
                        >
                            <?php echo esc_html($category->name); ?>
                        </button>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </div>
            </div>

            <!-- Formats -->
            <div class="photo-filter photo-filter--format" data-filter="format">
                <button
                    type="button"
                    class="photo-filter__trigger"
                    id="photo-format"
                    aria-expanded="false"
                    aria-controls="photo-format-options"
                    aria-haspopup="true"
                >
                    <span class="photo-filter__label">FORMATS</span>
                    <span class="photo-filter__chevron" aria-hidden="true"></span>
                </button>

                <div class="photo-filter__options" id="photo-format-options" hidden>
                    <button type="button" class="photo-filter__option" data-value="">
                        Tous les formats
                    </button>

                    <?php
                    $formats = get_terms(array(
                        'taxonomy'   => 'format',
                        'hide_empty' => false,
                    ));

                    if (!is_wp_error($formats)) :
                        foreach ($formats as $format) :
                    ?>
                        <button
                            type="button"
                            class="photo-filter__option"
                            data-value="<?php echo esc_attr($format->slug); ?>"
                        >
                            <?php echo esc_html($format->name); ?>
                        </button>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </div>
            </div>

        </div>

        <!-- Tri -->
        <div class="photo-filter photo-filter--sort" data-filter="tri">
            <button
                type="button"
                class="photo-filter__trigger"
                id="photo-sort"
                aria-expanded="false"
                aria-controls="photo-sort-options"
                aria-haspopup="true"
            >
                <span class="photo-filter__label">TRIER PAR</span>
                <span class="photo-filter__chevron" aria-hidden="true"></span>
            </button>

            <div class="photo-filter__options" id="photo-sort-options" hidden>
                <button type="button" class="photo-filter__option" data-value="DESC">
                    À partir des plus récentes
                </button>
                <button type="button" class="photo-filter__option" data-value="ASC">
                    À partir des plus anciennes
                </button>
            </div>
        </div>

    </div>


        <?php
        $photos = new WP_Query(array(
            'post_type'      => 'photo',
            'post_status'    => 'publish',
            'posts_per_page' => 8,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ));
        ?>

        <?php if ($photos->have_posts()) : ?>

            <div class="photo-grid">

                <?php while ($photos->have_posts()) : $photos->the_post(); ?>

                    <article class="photo-card">

                        <a href="<?php the_permalink(); ?>" class="photo-card__link">

                            <?php
                            if (has_post_thumbnail()) {
                                the_post_thumbnail('large', array(
                                    'class'   => 'photo-card__image',
                                    'loading' => 'lazy',
                                ));
                            }
                            ?>

                        </a>

                    </article>

                <?php endwhile; ?>

            </div>

        <?php else : ?>

            <p class="photo-gallery__empty">
                Aucune photographie disponible pour le moment.
            </p>

        <?php endif; ?>

        <?php wp_reset_postdata(); ?>

    </section>

</main>

<?php get_footer(); ?>