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

</main>

<?php get_footer(); ?>