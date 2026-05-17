@extends('admin.layouts.sb-admin')

@section('title', __('Main menu'))

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">{{ __('Main menu') }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">{{ __('Main menu') }}</li>
        </ol>

        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2 flex-wrap">
                <i class="fas fa-bars"></i>
                <span>{{ __('Drag ☰ to reorder nodes') }}</span>
                <span id="mm-save-status" class="ms-md-auto small text-muted order-3 order-md-2"></span>
                <div class="ms-md-auto d-flex gap-2 order-2 order-md-3 w-100 w-md-auto justify-content-md-end">
                    <a href="{{ route('admin.main-menu.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-1"></i>{{ __('Add menu item') }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($tree->isEmpty() || $tree->get(0, collect())->isEmpty())
                    <p class="text-muted mb-0">{{ __('No menu items yet. Use the button above to add one.') }}</p>
                @else
                    <div id="mm-root">
                        <ol class="mm-list" id="mm-list-root">
                            @include('admin.pages.main-menu.partials.tree-node', [
                                'items' => $tree->get(0, collect()),
                                'tree'  => $tree,
                            ])
                        </ol>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <div
        id="admin-bootstrap-table-i18n"
        class="d-none"
        data-delete-confirm="{{ __('Delete this record?') }}"
        data-fallback-done="{{ __('Done.') }}"
        data-fallback-failed="{{ __('Request failed.') }}"
    ></div>
    @vite(['resources/themes/admin/assets/js/admin-bootstrap-table.js'])
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.3/Sortable.min.js"></script>
    <script>
        (function () {
            const saveOrderUrl = @json($saveOrderUrl);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const statusEl = document.getElementById('mm-save-status');

            function setStatus(text, cssClass) {
                if (!statusEl) {
                    return;
                }
                statusEl.textContent = text;
                statusEl.className = 'ms-md-auto small order-3 order-md-2 ' + cssClass;
            }

            function destroyAllMmSortables() {
                document.querySelectorAll('#mm-root .mm-list').forEach(function (el) {
                    const inst = Sortable.get(el);
                    if (inst) {
                        inst.destroy();
                    }
                });
            }

            function mountSortable(el) {
                new Sortable(el, {
                    group: 'mm-tree',
                    handle: '.mm-handle',
                    animation: 150,
                    fallbackOnBody: true,
                    swapThreshold: 0.65,
                    emptyInsertThreshold: 20,
                    onStart: function () {
                        const r = document.getElementById('mm-root');
                        if (r) {
                            r.classList.add('mm-is-dragging');
                        }
                    },
                    onEnd: function () {
                        const r = document.getElementById('mm-root');
                        if (r) {
                            r.classList.remove('mm-is-dragging');
                        }
                        window.setTimeout(function () {
                            reinitMmTreeSortables();
                            saveTree();
                        }, 0);
                    },
                });
            }

            function reinitMmTreeSortables() {
                destroyAllMmSortables();
                document.querySelectorAll('#mm-root .mm-list').forEach(mountSortable);
            }

            function serializeTree(ol) {
                const nodes = [];
                ol.querySelectorAll(':scope > li.mm-item').forEach(function (li) {
                    const node = { id: parseInt(li.dataset.id, 10) };
                    const nested = li.querySelector(':scope > ol.mm-list');
                    if (nested && nested.querySelectorAll(':scope > li').length > 0) {
                        node.children = serializeTree(nested);
                    }
                    nodes.push(node);
                });
                return nodes;
            }

            function saveTree() {
                const rootOl = document.getElementById('mm-list-root');
                if (!rootOl) {
                    return;
                }
                const nodes = serializeTree(rootOl);

                setStatus('{{ __('Saving…') }}', 'text-muted');

                fetch(saveOrderUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ nodes: nodes }),
                })
                    .then(function (response) {
                        return response
                            .json()
                            .then(function (body) {
                                return { ok: response.ok, body: body };
                            })
                            .catch(function () {
                                return { ok: response.ok, body: null };
                            });
                    })
                    .then(function (result) {
                        if (!result.ok) {
                            let msg = '{{ __('Error saving order.') }}';
                            if (result.body && result.body.errors && typeof result.body.errors === 'object') {
                                const keys = Object.keys(result.body.errors);
                                if (keys.length > 0) {
                                    const first = result.body.errors[keys[0]];
                                    if (Array.isArray(first) && typeof first[0] === 'string') {
                                        msg = first[0];
                                    }
                                }
                            } else if (result.body && typeof result.body.message === 'string') {
                                msg = result.body.message;
                            }
                            setStatus('{{ __('Error saving. Try again.') }}', 'text-danger');
                            if (typeof window.adminNotify === 'function') {
                                window.adminNotify(msg, 'danger');
                            }
                            window.setTimeout(function () {
                                window.location.reload();
                            }, 600);
                            return;
                        }
                        setStatus('{{ __('Saved') }}', 'text-success');
                        if (typeof window.adminNotify === 'function') {
                            window.adminNotify('{{ __('Order saved') }}', 'success');
                        }
                    })
                    .catch(function () {
                        setStatus('{{ __('Error saving. Try again.') }}', 'text-danger');
                        if (typeof window.adminNotify === 'function') {
                            window.adminNotify('{{ __('Error saving order.') }}', 'danger');
                        }
                        window.setTimeout(function () {
                            window.location.reload();
                        }, 600);
                    });
            }

            document.addEventListener('DOMContentLoaded', function () {
                reinitMmTreeSortables();
            });
        }());
    </script>
@endpush
