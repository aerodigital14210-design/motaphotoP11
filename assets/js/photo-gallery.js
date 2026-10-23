
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
