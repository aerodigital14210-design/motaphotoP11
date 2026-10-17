
document.addEventListener('DOMContentLoaded', function () {
    const filters = document.querySelectorAll('.photo-filter');

    function closeFilter(filter) {
        const trigger = filter.querySelector('.photo-filter__trigger');
        const options = filter.querySelector('.photo-filter__options');

        trigger.setAttribute('aria-expanded', 'false');
        options.hidden = true;
    }

    function closeAllFilters(except = null) {
        filters.forEach(function (filter) {
            if (filter !== except) {
                closeFilter(filter);
            }
        });
    }

    filters.forEach(function (filter) {
        const trigger = filter.querySelector('.photo-filter__trigger');
        const options = filter.querySelector('.photo-filter__options');
        const label = filter.querySelector('.photo-filter__label');
        const buttons = options.querySelectorAll('.photo-filter__option');

        const initialLabel = label.textContent.trim();

        trigger.addEventListener('click', function () {
            const isOpen = trigger.getAttribute('aria-expanded') === 'true';

            closeAllFilters();

            if (!isOpen) {
                trigger.setAttribute('aria-expanded', 'true');
                options.hidden = false;
            }
        });

        buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                const value = button.dataset.value;

                buttons.forEach(function (item) {
                    item.setAttribute(
                        'aria-pressed',
                        String(item === button)
                    );
                });

                label.textContent = value
                    ? button.textContent.trim()
                    : initialLabel;

                filter.dataset.value = value;

                closeFilter(filter);
                trigger.focus();

                // Événement réutilisable lors du filtrage via l'API.
                filter.dispatchEvent(new CustomEvent('photoFilterChange', {
                    bubbles: true,
                    detail: {
                        filter: filter.dataset.filter,
                        value: value
                    }
                }));
            });
        });

        filter.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeFilter(filter);
                trigger.focus();
            }

            if (event.key === 'ArrowDown' &&
                event.target === trigger &&
                trigger.getAttribute('aria-expanded') === 'false') {
                event.preventDefault();
                trigger.click();
                buttons[0]?.focus();
            }
        });
    });

    document.addEventListener('click', function (event) {
        if (!event.target.closest('.photo-filter')) {
            closeAllFilters();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeAllFilters();
        }
    });
});
