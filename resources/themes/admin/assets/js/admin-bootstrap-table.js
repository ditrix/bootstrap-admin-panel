/**
 * Admin bootstrap-table helpers (loaded after jQuery and bootstrap-table).
 */
window.adminBootstrapTableDelete = async function (url) {
    if (!window.confirm('Delete this record?')) {
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
    if (response.ok) {
        window.location.reload();
    }
};
