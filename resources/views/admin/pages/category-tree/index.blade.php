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

    <div class="modal fade" id="ctEditModal" tabindex="-1" aria-labelledby="ctEditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ctEditModalLabel">{{ __('Edit category') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <div class="modal-body">
                    <form id="ct-edit-form" method="post" action="#">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="ct_parent_id">{{ __('Parent') }}</label>
                            <select class="form-select" id="ct_parent_id" name="parent_id" required></select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="ct_title">{{ __('Title') }}</label>
                            <input type="text" class="form-control" id="ct_title" name="title" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="ct_slug">{{ __('Slug') }}</label>
                            <input type="text" class="form-control" id="ct_slug" name="slug" maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="ct_description">{{ __('Description') }}</label>
                            <textarea class="form-control" id="ct_description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" class="form-check-input" id="ct_is_active" name="is_active" value="1">
                            <label class="form-check-label" for="ct_is_active">{{ __('Active') }}</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary" form="ct-edit-form">{{ __('Update') }}</button>
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
            const saveOrderUrl = @json(route('admin.category-tree.save-order'));
            const updateUrlTemplate = @json($categoryTreeUpdateUrlTemplate);
            const categoryTreeMeta = @json($categoryTreeMetaForJs);
            const categoryTreeEditorData = @json($categoryTreeEditorForJs);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const statusEl = document.getElementById('ct-save-status');
            const editModalEl = document.getElementById('ctEditModal');
            const editForm = document.getElementById('ct-edit-form');
            const parentSelect = document.getElementById('ct_parent_id');
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
                statusEl.className = 'ms-auto small ' + cssClass;
            }

            function initSortable(el) {
                if (Sortable.get(el)) {
                    return;
                }
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
                const blocked = collectDescendantIds(editingId, nodes);
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
                const data = categoryTreeEditorData[nodeId];
                if (!data || !parentSelect || !editForm) {
                    return;
                }
                editingId = nodeId;
                parentSelect.innerHTML = buildParentOptionsHtml(categoryTreeMeta, nodeId);
                document.getElementById('ct_title').value = data.title || '';
                document.getElementById('ct_slug').value = data.slug || '';
                document.getElementById('ct_description').value = data.description || '';
                parentSelect.value = String(data.parent_id);
                const activeCb = document.getElementById('ct_is_active');
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
                    fd.set('is_active', document.getElementById('ct_is_active').checked ? '1' : '0');
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

            document.getElementById('ct-root') && document.getElementById('ct-root').addEventListener('click', function (e) {
                const btn = e.target.closest('.ct-btn-edit');
                if (!btn) {
                    return;
                }
                const id = parseInt(btn.getAttribute('data-ct-node-id'), 10);
                if (Number.isInteger(id) && id > 0) {
                    openEdit(id);
                }
            });

            document.addEventListener('DOMContentLoaded', function () {
                initNestedSortables();
            });
        }());
    </script>
@endpush
