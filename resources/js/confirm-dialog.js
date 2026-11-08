let modal = null;
let pending = null;
let previousFocus = null;

function setTone(tone) {
    const icon = modal.querySelector('[data-confirm-icon]');
    const alertIcon = modal.querySelector('[data-confirm-icon-alert]');
    const infoIcon = modal.querySelector('[data-confirm-icon-info]');
    const accept = modal.querySelector('[data-confirm-accept]');
    const safeTone = ['primary', 'warning', 'danger'].includes(tone) ? tone : 'primary';

    icon.className = `confirm-icon confirm-icon--${safeTone}`;

    if (safeTone === 'primary') {
        alertIcon.classList.add('hidden');
        infoIcon.classList.remove('hidden');
    } else {
        alertIcon.classList.remove('hidden');
        infoIcon.classList.add('hidden');
    }

    accept.className = `btn w-full sm:w-auto ${safeTone === 'danger' ? 'btn-danger-solid' : 'btn-primary'}`;
}

function openConfirm(form, submitter) {
    if (!modal) {
        return;
    }

    pending = { form, submitter: submitter ?? null };
    previousFocus = document.activeElement instanceof HTMLElement ? document.activeElement : null;

    const message = (form.getAttribute('data-confirm') || '').trim() || 'Lanjutkan tindakan ini?';
    const title = (form.getAttribute('data-confirm-title') || '').trim() || 'Konfirmasi tindakan';
    const label = (form.getAttribute('data-confirm-label') || '').trim() || 'Ya, lanjutkan';

    modal.querySelector('[data-confirm-title]').textContent = title;
    modal.querySelector('[data-confirm-message]').textContent = message;
    modal.querySelector('[data-confirm-accept]').textContent = label;

    const cancelLabel = modal.querySelector('.btn[data-confirm-cancel]');
    if (cancelLabel) {
        cancelLabel.textContent = (form.getAttribute('data-confirm-cancel') || '').trim() || 'Batal';
    }

    setTone(form.getAttribute('data-confirm-tone') || 'primary');

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');

    window.requestAnimationFrame(() => {
        (cancelLabel || modal.querySelector('[data-confirm-cancel]'))?.focus();
    });
}

function closeConfirm({ restoreFocus = true } = {}) {
    if (!modal) {
        return;
    }

    modal.classList.add('hidden');
    modal.classList.remove('flex');
    modal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('overflow-hidden');

    pending = null;

    if (restoreFocus && previousFocus instanceof HTMLElement) {
        previousFocus.focus();
    }

    previousFocus = null;
}

function acceptConfirm() {
    const current = pending;

    if (!current) {
        closeConfirm();
        return;
    }

    const { form, submitter } = current;
    closeConfirm({ restoreFocus: false });

    // Bypass the dialog only for the re-submission triggered below.
    form.dataset.confirmBypass = '1';

    try {
        if (typeof form.requestSubmit === 'function') {
            submitter ? form.requestSubmit(submitter) : form.requestSubmit();
        } else {
            form.submit();
        }
    } finally {
        delete form.dataset.confirmBypass;
    }
}

function initConfirmDialog() {
    modal = document.querySelector('[data-confirm-modal]');

    if (!modal) {
        return;
    }

    modal.querySelectorAll('[data-confirm-cancel]').forEach((button) => {
        button.addEventListener('click', () => closeConfirm());
    });

    modal.querySelector('[data-confirm-backdrop]')?.addEventListener('click', () => closeConfirm());
    modal.querySelector('[data-confirm-accept]')?.addEventListener('click', acceptConfirm);

    document.addEventListener('submit', (event) => {
        const form = event.target;

        if (!(form instanceof HTMLFormElement) || event.defaultPrevented) {
            return;
        }

        if (!form.hasAttribute('data-confirm') || form.dataset.confirmBypass === '1') {
            return;
        }

        event.preventDefault();
        openConfirm(form, event.submitter);
    });

    document.addEventListener('keydown', (event) => {
        if (modal.classList.contains('hidden')) {
            return;
        }

        if (event.key === 'Escape') {
            event.preventDefault();
            event.stopPropagation();
            closeConfirm();
            return;
        }

        if (event.key !== 'Tab') {
            return;
        }

        const focusable = [...modal.querySelectorAll(
            'button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])',
        )].filter((node) => node.offsetParent !== null);

        if (!focusable.length) {
            return;
        }

        const first = focusable[0];
        const last = focusable[focusable.length - 1];

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initConfirmDialog);
} else {
    initConfirmDialog();
}
