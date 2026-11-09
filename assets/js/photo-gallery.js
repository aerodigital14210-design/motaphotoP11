
document.addEventListener('DOMContentLoaded', function () {

    const gallery = document.querySelector('.photo-gallery');
    const grid = gallery?.querySelector('.photo-grid');
    const loadMoreButton = document.querySelector('#photo-load-more');

    if (!gallery || !grid || !window.nathalieMotaGallery?.apiUrl) {
        return;
    }

    const filters = {
        categorie: '',
        format: '',
        tri: 'DESC'
    };

    let currentPage = 1;
    let totalPages = 1;
    let activeRequest = null;

    /**
     * Crée une carte photographique.
     */
    
    function createPhotoCard(photo) {

        const article = document.createElement('article');
        article.className = 'photo-card';

        // Photographie
        if (photo.image) {
            const image = document.createElement('img');
            image.className = 'photo-card__image';
            image.src = photo.image;
            image.alt = photo.alt || photo.title || '';
            image.loading = 'lazy';

            article.appendChild(image);
        }

        // Voile au survol
        const overlay = document.createElement('div');
        overlay.className = 'photo-card__overlay';

        // Bouton plein écran
        const fullscreen = document.createElement('button');
        fullscreen.type = 'button';
        fullscreen.className = 'photo-card__fullscreen';
        fullscreen.dataset.photoId = String(photo.id);
        fullscreen.setAttribute(
            'aria-label',
            'Afficher en plein écran : ' + (photo.title || 'Photographie')
        );

        fullscreen.innerHTML = `
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <path d="M8 3H3v5M16 3h5v5M3 16v5h5M21 16v5h-5"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"/>
            </svg>
        `;

        // Lien vers la page individuelle
        const eye = document.createElement('a');
        eye.className = 'photo-card__eye';
        eye.href = photo.permalink;
        eye.setAttribute(
            'aria-label',
            'Voir la photographie : ' + (photo.title || 'Photographie')
        );

        eye.innerHTML = `
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"/>
                <circle cx="12" cy="12" r="3"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"/>
            </svg>
        `;

        // Référence et catégorie
        const info = document.createElement('div');
        info.className = 'photo-card__info';

        const reference = document.createElement('span');
        reference.className = 'photo-card__reference';
        reference.textContent = photo.reference || '';

        const category = document.createElement('span');
        category.className = 'photo-card__category';
        category.textContent = photo.category || '';

        info.appendChild(reference);
        info.appendChild(category);

        overlay.appendChild(fullscreen);
        overlay.appendChild(eye);
        overlay.appendChild(info);

        article.appendChild(overlay);

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
     * Met à jour la visibilité du bouton.
     */
    function updateLoadMoreButton() {
        if (!loadMoreButton) {
            return;
        }

        loadMoreButton.hidden = currentPage >= totalPages;
    }

    /**
     * Charge une page de photographies.
     *
     * reset = true : remplace les photos après un changement
     * de filtre.
     *
     * reset = false : ajoute les photos à la suite.
     */
    async function loadPhotos(reset = false) {

        if (activeRequest) {
            activeRequest.abort();
        }

        const controller = new AbortController();
        activeRequest = controller;

        const requestedPage = reset ? 1 : currentPage + 1;

        const url = new URL(window.nathalieMotaGallery.apiUrl);

        url.searchParams.set('categorie', filters.categorie);
        url.searchParams.set('format', filters.format);
        url.searchParams.set('tri', filters.tri);
        url.searchParams.set('page', String(requestedPage));

        grid.setAttribute('aria-busy', 'true');

        if (loadMoreButton) {
            loadMoreButton.disabled = true;
            loadMoreButton.textContent = 'Chargement...';

            if (reset) {
                loadMoreButton.hidden = true;
            }
        }

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

            // Ignore une réponse devenue obsolète.
            if (activeRequest !== controller) {
                return;
            }

            const fragment = document.createDocumentFragment();

            data.photos.forEach(function (photo) {
                fragment.appendChild(createPhotoCard(photo));
            });

            if (reset) {
                grid.replaceChildren(fragment);
            } else {
                grid.appendChild(fragment);
            }

            if (reset && data.photos.length === 0) {
                showMessage(
                    'Aucune photographie ne correspond à votre recherche.'
                );
            }

            currentPage = requestedPage;
            totalPages = Number(data.total_pages) || 0;

            updateLoadMoreButton();

        } catch (error) {

            if (error.name === 'AbortError') {
                return;
            }

            if (activeRequest !== controller) {
                return;
            }

            console.error(
                'Erreur de chargement des photos :',
                error
            );

            // En cas d'erreur, conserve les photos déjà présentes.
            if (reset) {
                showMessage('Impossible de charger les photographies.');
            }

        } finally {

            if (activeRequest === controller) {
                activeRequest = null;
                grid.removeAttribute('aria-busy');

                if (loadMoreButton) {
                    loadMoreButton.disabled = false;
                    loadMoreButton.textContent = 'Charger plus';

                    // Réaffiche le bouton si un chargement a échoué
                    // et qu'il reste des pages à récupérer.
                    if (currentPage < totalPages) {
                        loadMoreButton.hidden = false;
                    }
                }
            }
        }
    }

    /**
     * Changement de catégorie, format ou tri.
     */
    gallery.addEventListener('photoFilterChange', function (event) {

        const { filter, value } = event.detail;

        if (!Object.hasOwn(filters, filter)) {
            return;
        }

        filters[filter] = value || (filter === 'tri' ? 'DESC' : '');

        loadPhotos(true);
    });

    /**
     * Chargement de huit photographies supplémentaires.
     */
    if (loadMoreButton) {
        loadMoreButton.addEventListener('click', function () {

            if (activeRequest || currentPage >= totalPages) {
                return;
            }

            loadPhotos(false);
        });
    }

    /**
     * État initial : les huit premières photos sont déjà
     * générées par WordPress dans front-page.php.
     */
    if (loadMoreButton) {
        totalPages = loadMoreButton.hidden ? 1 : 2;
    }

});
