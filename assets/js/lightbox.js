
document.addEventListener('DOMContentLoaded', function () {

    
    const lightbox = document.querySelector('#photo-lightbox');

    const gallery = document.querySelector(
        '.photo-gallery, .single-photo__related'
    );

    if (!lightbox || !gallery || !window.nathalieMotaGallery?.apiUrl) {
        return;
    }


    const image = lightbox.querySelector('.photo-lightbox__image');
    const reference = lightbox.querySelector('.photo-lightbox__reference');
    const category = lightbox.querySelector('.photo-lightbox__category');

    const closeButton = lightbox.querySelector('.photo-lightbox__close');
    const prevButton = lightbox.querySelector('.photo-lightbox__prev');
    const nextButton = lightbox.querySelector('.photo-lightbox__next');

    const filters = {
        categorie: '',
        format: '',
        tri: 'DESC'
    };

    let photos = [];
    let currentIndex = 0;
    let previousFocus = null;
    let previousOverflow = '';
    let requestController = null;

    // Synchronisation avec les filtres existants.
    
    if (gallery.classList.contains('photo-gallery')) {

        gallery.addEventListener('photoFilterChange', function (event) {
            const { filter, value } = event.detail;

            if (Object.hasOwn(filters, filter)) {
                filters[filter] = value || (filter === 'tri' ? 'DESC' : '');
            }
        });

    }


    async function fetchAllPhotos(signal) {

        const allPhotos = [];
        let page = 1;
        let totalPages = 1;

        do {
            const url = new URL(window.nathalieMotaGallery.apiUrl);

            url.searchParams.set('categorie', filters.categorie);
            url.searchParams.set('format', filters.format);
            url.searchParams.set('tri', filters.tri);
            url.searchParams.set('page', String(page));

            const response = await fetch(url.toString(), { signal });

            if (!response.ok) {
                throw new Error('Erreur HTTP : ' + response.status);
            }

            const data = await response.json();

            if (!Array.isArray(data.photos)) {
                throw new Error('Réponse API invalide');
            }

            allPhotos.push(...data.photos);
            totalPages = Number(data.total_pages) || 0;

            page++;

        } while (page <= totalPages);

        return allPhotos;
    }

    function displayPhoto() {

        const photo = photos[currentIndex];

        if (!photo) {
            return;
        }

        image.src = photo.image || '';
        image.alt = photo.alt || photo.title || '';

        reference.textContent = photo.reference || '';
        category.textContent = photo.category || '';

        prevButton.disabled = currentIndex === 0;
        nextButton.disabled = currentIndex === photos.length - 1;
    }

    async function openLightbox(photoId, trigger) {

        if (requestController) {
            requestController.abort();
        }

        const controller = new AbortController();
        requestController = controller;

        try {
            const loadedPhotos = await fetchAllPhotos(controller.signal);

            if (requestController !== controller) {
                return;
            }

            const index = loadedPhotos.findIndex(function (photo) {
                return Number(photo.id) === Number(photoId);
            });

            if (index === -1) {
                return;
            }

            photos = loadedPhotos;
            currentIndex = index;

            previousFocus = trigger;
            previousOverflow = document.body.style.overflow;

            displayPhoto();

            lightbox.hidden = false;
            document.body.style.overflow = 'hidden';
            closeButton.focus();

        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Erreur de la lightbox :', error);
            }
        } finally {
            if (requestController === controller) {
                requestController = null;
            }
        }
    }

    function closeLightbox() {

        lightbox.hidden = true;
        image.removeAttribute('src');

        document.body.style.overflow = previousOverflow;

        if (previousFocus?.isConnected) {
            previousFocus.focus();
        }
    }

    function navigate(direction) {

        const nextIndex = currentIndex + direction;

        if (nextIndex < 0 || nextIndex >= photos.length) {
            return;
        }

        currentIndex = nextIndex;
        displayPhoto();
    }

    // Délégation : fonctionne aussi après "Charger plus".
    gallery.addEventListener('click', function (event) {

        const button = event.target.closest('.photo-card__fullscreen');

        if (!button) {
            return;
        }

        event.preventDefault();
        openLightbox(button.dataset.photoId, button);
    });

    closeButton.addEventListener('click', closeLightbox);

    prevButton.addEventListener('click', function () {
        navigate(-1);
    });

    nextButton.addEventListener('click', function () {
        navigate(1);
    });

    lightbox.addEventListener('click', function (event) {
        if (event.target === lightbox) {
            closeLightbox();
        }
    });

    document.addEventListener('keydown', function (event) {

        if (lightbox.hidden) {
            return;
        }

        if (event.key === 'Escape') {
            closeLightbox();
        }

        if (event.key === 'ArrowLeft') {
            navigate(-1);
        }

        if (event.key === 'ArrowRight') {
            navigate(1);
        }

        // Maintien du focus dans la fenêtre modale.
        if (event.key === 'Tab') {

            const focusable = [
                closeButton,
                prevButton,
                nextButton
            ].filter(function (button) {
                return !button.disabled;
            });

            const first = focusable[0];
            const last = focusable[focusable.length - 1];

            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        }
    });

    // Navigation tactile.
    let touchStartX = 0;
    let touchStartY = 0;

    image.addEventListener('touchstart', function (event) {
        touchStartX = event.changedTouches[0].screenX;
        touchStartY = event.changedTouches[0].screenY;
    }, { passive: true });

    image.addEventListener('touchend', function (event) {

        const deltaX = event.changedTouches[0].screenX - touchStartX;
        const deltaY = event.changedTouches[0].screenY - touchStartY;

        if (Math.abs(deltaX) > 50 && Math.abs(deltaX) > Math.abs(deltaY)) {
            navigate(deltaX < 0 ? 1 : -1);
        }
    }, { passive: true });

});
