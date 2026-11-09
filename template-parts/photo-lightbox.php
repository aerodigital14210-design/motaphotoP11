<!-- Lightbox des photographies -->
<div
    id="photo-lightbox"
    class="photo-lightbox"
    role="dialog"
    aria-modal="true"
    aria-label="Visionneuse de photographies"
    hidden
>
    <div class="photo-lightbox__container">

        <button
            type="button"
            class="photo-lightbox__close"
            aria-label="Fermer la visionneuse"
        >
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M3 3L21 21M21 3L3 21"
                      stroke="currentColor"
                      stroke-width="3"
                      stroke-linecap="round"/>
            </svg>
        </button>

        <button
            type="button"
            class="photo-lightbox__prev"
            aria-label="Photographie précédente"
        >
            <svg viewBox="0 0 26 18" aria-hidden="true">
                <path d="M25 9H2M9 2L2 9L9 16"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"/>
            </svg>
            <span>Précédente</span>
        </button>

        <figure class="photo-lightbox__figure">
            <img
                class="photo-lightbox__image"
                src=""
                alt=""
            >

            <figcaption class="photo-lightbox__info">
                <span class="photo-lightbox__reference"></span>
                <span class="photo-lightbox__category"></span>
            </figcaption>
        </figure>

        <button
            type="button"
            class="photo-lightbox__next"
            aria-label="Photographie suivante"
        >
            <span>Suivante</span>
            <svg viewBox="0 0 26 18" aria-hidden="true">
                <path d="M1 9H24M17 2L24 9L17 16"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"/>
            </svg>
        </button>

    </div>
</div>