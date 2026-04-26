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
                    <button type="button" class="btn btn-sm btn-primary" id="mm-btn-open-add" data-bs-toggle="modal" data-bs-target="#mmAddModal">
                        <i class="fas fa-plus me-1"></i>{{ __('Add menu item') }}
                    </button>
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

    <div class="modal fade" id="mmAddModal" tabindex="-1" aria-labelledby="mmAddModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mmAddModalLabel">{{ __('Add menu item') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <form method="post" action="{{ route('admin.main-menu.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label" for="mm_add_parent_id">{{ __('Parent') }}</label>
                            <select class="form-select" id="mm_add_parent_id" name="parent_id" required>
                                {!! $parentOptionsHtml !!}
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="mm_add_title">{{ __('Title') }}</label>
                            <input type="text" class="form-control" id="mm_add_title" name="title" required maxlength="255" value="{{ old('title') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="mm_add_slug">{{ __('Slug') }}</label>
                            <input type="text" class="form-control" id="mm_add_slug" name="slug" maxlength="255" value="{{ old('slug') }}">
                        </div>
                        <div class="mb-3 form-check">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" class="form-check-input" id="mm_add_is_active" name="is_active" value="1" @checked(old('is_active', true))>
                            <label class="form-check-label" for="mm_add_is_active">{{ __('Active') }}</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="mmEditModal" tabindex="-1" aria-labelledby="mmEditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mmEditModalLabel">{{ __('Edit menu item') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <div class="modal-body">
                    <form id="mm-edit-form" method="post" action="#">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="mm_parent_id">{{ __('Parent') }}</label>
                            <select class="form-select" id="mm_parent_id" name="parent_id" required></select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="mm_title">{{ __('Title') }}</label>
                            <input type="text" class="form-control" id="mm_title" name="title" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="mm_slug">{{ __('Slug') }}</label>
                            <input type="text" class="form-control" id="mm_slug" name="slug" maxlength="255">
                        </div>
                        <div class="mb-3 form-check">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" class="form-check-input" id="mm_is_active" name="is_active" value="1">
                            <label class="form-check-label" for="mm_is_active">{{ __('Active') }}</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary" form="mm-edit-form">{{ __('Update') }}</button>
                </div>
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
            const saveOrderUrl = @json(route('admin.main-menu.save-order'));
            const updateUrlTemplate = @json($mainMenuUpdateUrlTemplate);
            const mainMenuMeta = @json($mainMenuMetaForJs);
            const mainMenuEditorData = @json($mainMenuEditorForJs);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const statusEl = document.getElementById('mm-save-status');
            const editModalEl = document.getElementById('mmEditModal');
            const editForm = document.getElementById('mm-edit-form');
            const parentSelect = document.getElementById('mm_parent_id');
            let editingId = null;
            let editModal = null;
            if (editModalEl && window.bootstrap) {
                editModal = new window.bootstrap.Modal(editModalEl);
            }

            function setStatus(text, cssClass) {
                if (!statusEl) {
                    return;
                }
                statusEl.textContent = text;
                statusEl.className = 'ms-md-auto small ' + cssClass;
            }

            function initSortable(el) {
                if (Sortable.get(el)) {
                    return;
                }
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
                        initNestedSortables();
                        saveTree();
                    },
                });
            }

            function initNestedSortables() {
                document.querySelectorAll('#mm-root .mm-list').forEach(initSortable);
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
                    if (!response.ok) {
                        throw new Error('Server error');
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
                });
            }

            function collectDescendantIds(rootId, nodes) {
                const byParent = new Map();
                nodes.forEach(function (n) {
                    const p = n.parent_id;
                    if (!byParent.has(p)) {
                        byParent.set(p, []);
                    }
                    byParent.get(p).push(n);
                });
                const blocked = new Set([rootId]);
                const stack = [rootId];
                while (stack.length) {
                    const cur = stack.pop();
                    (byParent.get(cur) || []).forEach(function (c) {
                        if (!blocked.has(c.id)) {
                            blocked.add(c.id);
                            stack.push(c.id);
                        }
                    });
                }
                return blocked;
            }

            function buildParentOptionsHtml(nodes, editingId) {
                const blocked = editingId ? collectDescendantIds(editingId, nodes) : new Set();
                const byParent = new Map();
                nodes.forEach(function (n) {
                    if (blocked.has(n.id)) {
                        return;
                    }
                    const p = n.parent_id;
                    if (!byParent.has(p)) {
                        byParent.set(p, []);
                    }
                    byParent.get(p).push(n);
                });
                byParent.forEach(function (list) {
                    list.sort(function (a, b) {
                        if (a.sort_no !== b.sort_no) {
                            return a.sort_no - b.sort_no;
                        }
                        return a.id - b.id;
                    });
                });
                function escHtml(s) {
                    return String(s)
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/"/g, '&quot;');
                }
                const parts = ['<option value="0">', @json(__('Root')), '</option>'];
                function walk(parentId, depth) {
                    (byParent.get(parentId) || []).forEach(function (n) {
                        const indent = depth > 0 ? ('\u2014 '.repeat(depth)) : '';
                        const label = (indent ? indent + ' ' : '') + escHtml(n.title);
                        parts.push(
                            '<option value="' + String(n.id) + '">',
                            label,
                            '</option>',
                        );
                        walk(n.id, depth + 1);
                    });
                }
                walk(0, 0);
                return parts.join('');
            }

            function openEdit(nodeId) {
                const data = mainMenuEditorData[nodeId];
                if (!data || !parentSelect || !editForm) {
                    return;
                }
                editingId = nodeId;
                parentSelect.innerHTML = buildParentOptionsHtml(mainMenuMeta, nodeId);
                document.getElementById('mm_title').value = data.title || '';
                document.getElementById('mm_slug').value = data.slug || '';
                parentSelect.value = String(data.parent_id);
                const activeCb = document.getElementById('mm_is_active');
                if (activeCb) {
                    activeCb.checked = !!data.is_active;
                }
                if (editModal) {
                    editModal.show();
                }
            }

            if (editForm) {
                editForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    if (!editingId) {
                        return;
                    }
                    const url = updateUrlTemplate.replace('__ID__', String(editingId));
                    const fd = new FormData(editForm);
                    fd.append('_method', 'PUT');
                    fd.set('is_active', document.getElementById('mm_is_active').checked ? '1' : '0');
                    fetch(url, {
                        method: 'POST',
                        body: fd,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    })
                    .then(function (response) {
                        return response.json().then(function (body) {
                            return { response: response, body: body };
                        });
                    })
                    .then(function (r) {
                        let msg;
                        if (r.body && r.body.errors && typeof r.body.errors === 'object') {
                            const keys = Object.keys(r.body.errors);
                            if (keys.length > 0) {
                                const first = r.body.errors[keys[0]];
                                if (Array.isArray(first) && typeof first[0] === 'string') {
                                    msg = first[0];
                                }
                            }
                        }
                        if (!msg) {
                            msg = (r.body && r.body.message)
                                ? r.body.message
                                : (r.response.ok ? '{{ __('Done.') }}' : '{{ __('Request failed.') }}');
                        }
                        if (typeof window.adminNotify === 'function') {
                            window.adminNotify(msg, r.response.ok ? 'success' : 'danger');
                        }
                        if (r.response.ok) {
                            if (editModal) {
                                editModal.hide();
                            }
                            window.setTimeout(function () {
                                window.location.reload();
                            }, 400);
                        }
                    })
                    .catch(function () {
                        if (typeof window.adminNotify === 'function') {
                            window.adminNotify('{{ __('Request failed.') }}', 'danger');
                        }
                    });
                });
            }

            const mmRoot = document.getElementById('mm-root');
            if (mmRoot) {
                mmRoot.addEventListener('click', function (e) {
                    const btn = e.target.closest('.mm-btn-edit');
                    if (!btn) {
                        return;
                    }
                    const id = parseInt(btn.getAttribute('data-mm-node-id'), 10);
                    if (Number.isInteger(id) && id > 0) {
                        openEdit(id);
                    }
                });
            }

            document.addEventListener('DOMContentLoaded', function () {
                initNestedSortables();
            });
        }());
    </script>
@endpush
