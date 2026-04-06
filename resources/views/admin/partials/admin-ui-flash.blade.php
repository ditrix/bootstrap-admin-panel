@php
    $adminUiFlashItems = [];
    if (session('success')) {
        $adminUiFlashItems[] = ['type' => 'success', 'message' => session('success')];
    }
    if (session('error')) {
        $adminUiFlashItems[] = ['type' => 'danger', 'message' => session('error')];
    }
    if (session('status')) {
        $statusType = session('status_notify_variant', 'success');
        $adminUiFlashItems[] = ['type' => $statusType, 'message' => session('status')];
    }
@endphp

@if ($adminUiFlashItems !== [])
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const items = @json($adminUiFlashItems);
            items.forEach((item) => window.adminNotify(item.message, item.type));
        });
    </script>
@endif
