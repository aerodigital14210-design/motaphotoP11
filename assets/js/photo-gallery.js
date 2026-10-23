
document.addEventListener('DOMContentLoaded', function () {

    const gallery = document.querySelector('.photo-gallery');
    const grid = gallery?.querySelector('.photo-grid');

    if (!gallery || !grid || !window.nathalieMotaGallery?.apiUrl) {
        return;
    }

    const filters = {
        categorie: '',
        format: '',
        tri: 'DESC'
    };

    let activeRequest = null;

    /**
     * Crée une carte photo sans injecter de HTML non fiable.
     */
    function createPhotoCard(photo) {
        const article = document.createElement('article');
        article.className = 'photo-card';

        const link = document.createElement('a');
        link.className = 'photo-card__link';
        link.href = photo.permalink;

        if (photo.image) {
            const image = document.createElement('img');
            image.className = 'photo-card__image';
            image.src = photo.image;
            image.alt = photo.alt || photo.title || '';
            image.loading = 'lazy';

            link.appendChild(image);
        }

        article.appendChild(link);

        return article;
    }

    /**
     * Affiche un message dans la galerie.
     */
    function showMessage(message) {
        const paragraph = document.createElement('p');
        paragraph.className = 'photo-gallery__empty';
        paragraph.textContent = message;

        grid.replaceChildren(paragraph);
    }

    /**
     * Charge les photos correspondant aux filtres.
     */
    async function loadPhotos() {

        // Annule la requête précédente si nécessaire.
        if (activeRequest) {
            activeRequest.abort();
        }

        const controller = new AbortController();
        activeRequest = controller;

        const url = new URL(window.nathalieMotaGallery.apiUrl);

        url.searchParams.set('categorie', filters.categorie);
        url.searchParams.set('format', filters.format);
        url.searchParams.set('tri', filters.tri);
        url.searchParams.set('page', '1');

        grid.setAttribute('aria-busy', 'true');

        try {
            const response = await fetch(url.toString(), {
                signal: controller.signal
            });

            if (!response.ok) {
                throw new Error('Erreur HTTP : ' + response.status);
            }

            const data = await response.json();

            if (!Array.isArray(data.photos)) {
                throw new Error('Réponse API invalide');
            }

            if (data.photos.length === 0) {
                showMessage('Aucune photographie ne correspond à votre recherche.');
                return;
            }

            const fragment = document.createDocumentFragment();

            data.photos.forEach(function (photo) {
                fragment.appendChild(createPhotoCard(photo));
            });

            grid.replaceChildren(fragment);

        } catch (error) {

            if (error.name === 'AbortError') {
                return;
            }

            console.error('Erreur de chargement des photos :', error);

            showMessage('Impossible de charger les photographies.');

        } finally {

            if (activeRequest === controller) {
                activeRequest = null;
                grid.removeAttribute('aria-busy');
            }
        }
    }

    /**
     * Écoute les changements des menus personnalisés.
     */
    gallery.addEventListener('photoFilterChange', function (event) {

        const { filter, value } = event.detail;

        if (!Object.hasOwn(filters, filter)) {
            return;
        }

        filters[filter] = value || (filter === 'tri' ? 'DESC' : '');

        loadPhotos();
    });

});
