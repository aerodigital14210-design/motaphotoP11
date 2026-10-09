
document.addEventListener('DOMContentLoaded', () => {

    const toggle = document.querySelector('.menu-toggle');
    const navigation = document.querySelector('.main-navigation');

    if (!toggle || !navigation) return;

    const links = navigation.querySelectorAll('a, button');

    function setMenuState(open) {

        toggle.classList.toggle('is-active', open);
        navigation.classList.toggle('is-open', open);
        document.body.classList.toggle('menu-open', open);

        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute(
            'aria-label',
            open ? 'Fermer le menu' : 'Ouvrir le menu'
        );

        if (open) {
            links[0]?.focus();
        } else if (navigation.contains(document.activeElement)) {
            toggle.focus();
        }
    }

    toggle.addEventListener('click', () => {
        const isOpen = navigation.classList.contains('is-open');
        setMenuState(!isOpen);
    });

    // Fermer le menu après sélection
    links.forEach(link => {
        link.addEventListener('click', () => {
            setMenuState(false);
        });
    });

    // Fermer avec la touche Échap
    document.addEventListener('keydown', event => {
        if (
            event.key === 'Escape' &&
            navigation.classList.contains('is-open')
        ) {
            setMenuState(false);
            toggle.focus();
        }
    });

    // Fermer lors du passage en desktop
    const desktopQuery = window.matchMedia('(min-width: 768px)');

    desktopQuery.addEventListener('change', event => {
        if (event.matches) {
            setMenuState(false);
        }
    });

});
