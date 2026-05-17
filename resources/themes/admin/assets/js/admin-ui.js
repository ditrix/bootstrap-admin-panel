/**
 * Admin UI: toast notifications (top-end) and reusable confirm modal (Bootstrap 5).
 */
const NOTIFY_DURATION_MS = 3000;

const NOTIFY_VARIANT_CLASS = {
    success: 'admin-ui-notify--success',
    danger: 'admin-ui-notify--danger',
    info: 'admin-ui-notify--info',
    warning: 'admin-ui-notify--warning',
};

/**
 * @param {string} message
 * @param {'success'|'danger'|'info'|'warning'} [variant='info']
 */
function adminNotify(message, variant = 'info') {
    const stack = document.getElementById('admin-ui-notify-stack');
    if (!stack || !message) {
        return;
    }

    const variantClass = NOTIFY_VARIANT_CLASS[variant] ?? NOTIFY_VARIANT_CLASS.info;
    const el = document.createElement('div');
    el.className = `admin-ui-notify ${variantClass}`;
    el.setAttribute('role', 'status');
    el.textContent = message;
    stack.appendChild(el);

    requestAnimationFrame(() => {
        el.classList.add('admin-ui-notify--visible');
    });

    window.setTimeout(() => {
        el.classList.remove('admin-ui-notify--visible');
        el.addEventListener(
            'transitionend',
            () => {
                el.remove();
            },
            { once: true },
        );
    }, NOTIFY_DURATION_MS);
}

/**
 * @param {{ type: 'yes_no'|'ok'|'close'|'yes_no_cancel', title?: string, message: string, labels?: Record<string, string> }} options
 * @returns {Promise<boolean|string|null>}
 */
function adminUiDialog(options) {
    const modalEl = document.getElementById('admin-ui-modal');
    const titleEl = document.getElementById('admin-ui-modal-title');
    const bodyEl = document.getElementById('admin-ui-modal-body');
    const footerEl = document.getElementById('admin-ui-modal-footer');

    if (!modalEl || !titleEl || !bodyEl || !footerEl || typeof window.bootstrap === 'undefined') {
        return Promise.resolve(null);
    }

    const labelsFromDom = modalEl.dataset || {};
    const defaultLabels = {
        yes: labelsFromDom.labelYes ?? 'Yes',
        no: labelsFromDom.labelNo ?? 'No',
        cancel: labelsFromDom.labelCancel ?? 'Cancel',
        ok: labelsFromDom.labelOk ?? 'OK',
        close: labelsFromDom.labelClose ?? 'Close',
    };
    const labels = { ...defaultLabels, ...options.labels };

    titleEl.textContent = options.title ?? '';
    bodyEl.textContent = options.message ?? '';
    footerEl.innerHTML = '';

    return new Promise((resolve) => {
        let settled = false;
        const finish = (value) => {
            if (settled) {
                return;
            }
            settled = true;
            window.bootstrap.Modal.getOrCreateInstance(modalEl).hide();
            resolve(value);
        };

        const addBtn = (text, className, value) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = className;
            btn.textContent = text;
            btn.addEventListener('click', () => finish(value));
            footerEl.appendChild(btn);
        };

        switch (options.type) {
            case 'yes_no':
                addBtn(labels.no, 'btn btn-outline-secondary', false);
                addBtn(labels.yes, 'btn btn-primary', true);
                break;
            case 'ok':
                addBtn(labels.ok, 'btn btn-primary', undefined);
                break;
            case 'close':
                addBtn(labels.close, 'btn btn-outline-secondary', undefined);
                break;
            case 'yes_no_cancel':
                addBtn(labels.cancel, 'btn btn-outline-secondary', 'cancel');
                addBtn(labels.no, 'btn btn-outline-secondary', 'no');
                addBtn(labels.yes, 'btn btn-primary', 'yes');
                break;
            default:
                finish(null);

                return;
        }

        const onHidden = () => {
            modalEl.removeEventListener('hidden.bs.modal', onHidden);
            if (!settled) {
                settled = true;
                if (options.type === 'yes_no') {
                    resolve(false);
                } else if (options.type === 'yes_no_cancel') {
                    resolve('cancel');
                } else {
                    resolve(null);
                }
            }
        };
        modalEl.addEventListener('hidden.bs.modal', onHidden);

        window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
    });
}

window.adminNotify = adminNotify;
window.adminUiDialog = adminUiDialog;
