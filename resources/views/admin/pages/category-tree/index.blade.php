@extends('admin.layouts.sb-admin')

@section('title', __('Category Tree'))

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">{{ __('Category Tree') }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">{{ __('Category Tree') }}</li>
        </ol>

        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-sitemap"></i>
                <span>{{ __('Drag ☰ to reorder nodes') }}</span>
                <span id="ct-save-status" class="ms-auto small text-muted"></span>
            </div>
            <div class="card-body">
                @if($tree->isEmpty())
                    <p class="text-muted mb-0">{{ __('No categories found.') }}</p>
                @else
                    <div id="ct-root">
                        <ol class="ct-list" id="ct-list-root">
                            @include('admin.pages.category-tree.partials.tree-node', [
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
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.3/Sortable.min.js"></script>
    <script>
        (function () {
            const saveOrderUrl = @json(route('admin.category-tree.save-order'));
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const statusEl = document.getElementById('ct-save-status');

            function setStatus(text, cssClass) {
                if (!statusEl) return;
                statusEl.textContent = text;
                statusEl.className = 'ms-auto small ' + cssClass;
            }

            function initSortable(el) {
                if (Sortable.get(el)) return;
                new Sortable(el, {
                    group: 'ct-tree',
                    handle: '.ct-handle',
                    animation: 150,
                    fallbackOnBody: true,
                    swapThreshold: 0.65,
                    emptyInsertThreshold: 20,
                    onStart: function () {
                        document.getElementById('ct-root').classList.add('ct-is-dragging');
                    },
                    onEnd: function () {
                        document.getElementById('ct-root').classList.remove('ct-is-dragging');
                        initNestedSortables();
                        saveTree();
                    },
                });
            }

            function initNestedSortables() {
                document.querySelectorAll('#ct-root .ct-list').forEach(initSortable);
            }

            function serializeTree(ol) {
                const nodes = [];
                ol.querySelectorAll(':scope > li.ct-item').forEach(function (li) {
                    const node = { id: parseInt(li.dataset.id, 10) };
                    const nested = li.querySelector(':scope > ol.ct-list');
                    if (nested && nested.querySelectorAll(':scope > li').length > 0) {
                        node.children = serializeTree(nested);
                    }
                    nodes.push(node);
                });
                return nodes;
            }

            function saveTree() {
                const rootOl = document.getElementById('ct-list-root');
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
                    if (!response.ok) throw new Error('Server error');
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
                });
            }

            document.addEventListener('DOMContentLoaded', function () {
                initNestedSortables();
            });
        }());
    </script>
@endpush
