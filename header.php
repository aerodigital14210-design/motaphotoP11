
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main-content">
    Aller au contenu
</a>

<header class="site-header" id="site-header">

    <div class="header-container">

        <!-- Logo -->
        <a
            class="site-logo"
            href="<?php echo esc_url(home_url('/')); ?>"
            aria-label="Nathalie Mota - Accueil"
        >
            <img
                src="<?php echo esc_url(
                    get_template_directory_uri()
                    . '/assets/images/logo.png'
                ); ?>"
                alt="Nathalie Mota"
                width="216"
                height="14"
            >
        </a>

        <!-- Bouton menu mobile -->
        <button
            class="menu-toggle"
            type="button"
            aria-label="Ouvrir le menu"
            aria-expanded="false"
            aria-controls="primary-navigation"
        >
            <span class="menu-toggle-line"></span>
            <span class="menu-toggle-line"></span>
            <span class="menu-toggle-line"></span>
        </button>

        <!-- Navigation -->
        <nav
            class="main-navigation"
            id="primary-navigation"
            aria-label="Navigation principale"
        >
            <ul>
                <li>
                    <a href="<?php echo esc_url(
                        home_url('/')
                    ); ?>">
                        Accueil
                    </a>
                </li>

                <li>
                    <a href="<?php echo esc_url(
                        home_url('/a-propos/')
                    ); ?>">
                        À propos
                    </a>
                </li>

                <li>
                    <button
                        type="button"
                        class="open-contact-modal"
                    >
                        Contact
                    </button>
                </li>
            </ul>
        </nav>

    </div>

</header>
