/**
 * Admin bootstrap-table helpers (loaded after jQuery and bootstrap-table).
 */
window.adminBootstrapTableBooleanIcon = function (value) {
    const truthy =
        value === true ||
        value === 1 ||
        value === '1' ||
        String(value).toLowerCase() === 'true';

    if (truthy) {
        return '<span class="admin-bootstrap-table-boolean"><i style="color:green" class="dripicons-checkmark" aria-hidden="true"></i></span>';
    }

    return '<span class="admin-bootstrap-table-boolean"><i style="color:red" class="dripicons-cross" aria-hidden="true"></i></span>';
};

const $ = window.jQuery;

if ($) {
    $(() => {
        $('table.admin-bootstrap-table__grid').on('post-header.bs.table', function () {
            const $wrap = $(this).closest('.bootstrap-table');
            const $searchInput = $wrap.find('.fixed-table-toolbar input.search-input');
            if ($searchInput.length) {
                $searchInput.attr({ type: 'text', inputmode: 'search', autocomplete: 'off' });
            }
            const $btn = $wrap.find('.fixed-table-toolbar button[name="clearSearch"]');
            if (!$btn.length) {
                return;
            }
            $btn.empty();
            $btn.append(document.createTextNode('\u00d7'));
            $btn.attr('title', 'Clear');
            $btn.attr('aria-label', 'Clear search');
            $btn.addClass('admin-bootstrap-table__search-clear');
        });
    });
}

window.adminBootstrapTableDelete = async function (url) {
    const confirmed = await window.adminUiDialog({
        type: 'yes_no',
        message: 'Delete this record?',
    });
    if (!confirmed) {
        return;
    }
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!token) {
        return;
    }
    const body = new FormData();
    body.append('_token', token);
    body.append('_method', 'DELETE');
    const response = await fetch(url, {
        method: 'POST',
        body,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            Accept: 'application/json',
        },
    });

    let payload = null;
    try {
        payload = await response.json();
    } catch {
        // Response may be empty or non-JSON.
    }

    const fallbackMessage = response.ok ? 'Done.' : 'Request failed.';
    const message = typeof payload?.message === 'string' ? payload.message : fallbackMessage;

    if (typeof window.adminNotify === 'function') {
        window.adminNotify(message, response.ok ? 'success' : 'danger');
    }

    if (response.ok) {
        window.setTimeout(() => {
            window.location.reload();
        }, 400);
    }
};
