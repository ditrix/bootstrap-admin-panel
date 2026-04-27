@php
    $requirePassword = $requirePassword ?? false;
@endphp
<div class="mb-3">
    <label class="form-label" for="password">{{ __('New password') }}</label>
    <div class="input-group">
        <input
            type="password"
            class="form-control @error('password') is-invalid @enderror"
            id="password"
            name="password"
            @if($requirePassword) required @endif
            autocomplete="new-password"
        >
        <button
            class="btn btn-outline-secondary"
            type="button"
            data-admin-password-toggle="password"
            title="{{ __('Show password') }}"
            aria-label="{{ __('Show password') }}"
        >
            <i class="fas fa-eye" aria-hidden="true"></i>
        </button>
        @error('password')
            <div class="invalid-feedback d-block w-100">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3">
    <label class="form-label" for="password_confirmation">{{ __('Confirm password') }}</label>
    <div class="input-group">
        <input
            type="password"
            class="form-control @error('password_confirmation') is-invalid @enderror"
            id="password_confirmation"
            name="password_confirmation"
            @if($requirePassword) required @endif
            autocomplete="new-password"
        >
        <button
            class="btn btn-outline-secondary"
            type="button"
            data-admin-password-toggle="password_confirmation"
            title="{{ __('Show password') }}"
            aria-label="{{ __('Show password') }}"
        >
            <i class="fas fa-eye" aria-hidden="true"></i>
        </button>
        @error('password_confirmation')
            <div class="invalid-feedback d-block w-100">{{ $message }}</div>
        @enderror
    </div>
</div>
@push('scripts')
    <script>
        (function () {
            function bindToggle(root) {
                root = root || document;
                root.querySelectorAll('[data-admin-password-toggle]').forEach(function (btn) {
                    if (btn.dataset.adminPwToggleBound) {
                        return;
                    }
                    btn.dataset.adminPwToggleBound = '1';
                    btn.addEventListener('click', function () {
                        const id = this.getAttribute('data-admin-password-toggle');
                        const input = id && document.getElementById(id);
                        if (!input) {
                            return;
                        }
                        const show = input.type === 'password';
                        input.type = show ? 'text' : 'password';
                        const i = this.querySelector('i');
                        if (i) {
                            i.classList.remove('fa-eye', 'fa-eye-slash');
                            i.classList.add(show ? 'fa-eye-slash' : 'fa-eye');
                        }
                    });
                });
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function () { bindToggle(); });
            } else {
                bindToggle();
            }
        }());
    </script>
@endpush
