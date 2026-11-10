
<?php
/**
 * Modale de contact du site Nathalie Mota.
 *
 * @package Nathalie_Mota
 */
?>

<div
    id="contact-modal"
    class="contact-modal"
    role="dialog"
    aria-modal="true"
    aria-label="Formulaire de contact"
    aria-hidden="true"
    hidden
>
    <div class="contact-modal__overlay">

        <div class="contact-modal__content">


            <div class="contact-modal__header">
                <img
                    src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/contact-header.png'); ?>"
                    alt=""
                    class="contact-modal__header-image"
                    aria-hidden="true"
                >
            </div>

            <button
                type="button"
                class="contact-modal__close"
                aria-label="Fermer le formulaire de contact"
            >
                &times;
            </button>

            <div class="contact-modal__form">
                <?php
                echo do_shortcode(
                    '[contact-form-7 id="94a02e2" title="Contact – Nathalie Mota"]'
                );
                ?>
            </div>

        </div>

    </div>
</div>
