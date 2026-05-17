<div id="admin-ui-notify-stack" aria-live="polite" aria-relevant="additions"></div>

<div
    class="modal fade"
    id="admin-ui-modal"
    tabindex="-1"
    aria-labelledby="admin-ui-modal-title"
    aria-hidden="true"
    data-label-yes="{{ __('Yes') }}"
    data-label-no="{{ __('No') }}"
    data-label-cancel="{{ __('Cancel') }}"
    data-label-ok="{{ __('OK') }}"
    data-label-close="{{ __('Close') }}"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border">
            <div class="modal-header">
                <h5 class="modal-title" id="admin-ui-modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <div class="modal-body" id="admin-ui-modal-body"></div>
            <div class="modal-footer d-flex flex-wrap justify-content-end" id="admin-ui-modal-footer"></div>
        </div>
    </div>
</div>
