
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.querySelector('#contact-modal');

    if (!modal) {
        return;
    }

    const closeButton = modal.querySelector('.contact-modal__close');
    let previousFocus = null;
    let previousOverflow = '';

    function openModal(trigger) {
        previousFocus = trigger;
        previousOverflow = document.body.style.overflow;

        const reference = trigger.dataset.photoReference || '';
        const referenceField = modal.querySelector('#contact-reference');

        if (referenceField) {
            referenceField.value = reference;
        }

        modal.hidden = false;
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        closeButton.focus();
    }

    function closeModal() {
        modal.hidden = true;
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = previousOverflow;

        if (previousFocus && previousFocus.isConnected) {
            previousFocus.focus();
        }
    }

    document.addEventListener('click', function (event) {
        const trigger = event.target.closest(
        '.single-photo__contact-button, .open-contact-modal'
    );

        if (trigger) {
            event.preventDefault();
            openModal(trigger);
            return;
        }

        if (
            !modal.hidden &&
            event.target === modal
        ) {
            closeModal();
        }
    });

    closeButton.addEventListener('click', closeModal);

    document.addEventListener('keydown', function (event) {
        if (!modal.hidden && event.key === 'Escape') {
            closeModal();
        }
    });
});
