@foreach($items as $item)
    <li class="ct-item" data-id="{{ $item->id }}">
        <div class="ct-node">
            <span class="ct-handle" title="{{ __('Drag to reorder') }}">☰</span>
            <span class="ct-id text-muted">{{ $item->id }}</span>
            <span class="ct-title">{{ $item->title }}</span>
            <span class="ct-active" title="{{ __('Active') }}">
                @if($item->is_active)
                    <i class="dripicons-checkmark" style="color:green" aria-hidden="true"></i>
                @else
                    <i class="dripicons-cross" style="color:red" aria-hidden="true"></i>
                @endif
            </span>
            <span class="ct-node-actions">
                <a href="{{ route('admin.category-tree.edit', $item) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Edit') }}">
                    <i class="fas fa-edit"></i>
                </a>
                <button type="button" class="btn btn-sm btn-outline-danger ct-btn-delete" title="{{ __('Delete') }}"
                    onclick="adminBootstrapTableDelete(@js(route('admin.category-tree.destroy', $item)))">
                    <i class="fas fa-trash"></i>
                </button>
            </span>
        </div>
        <ol class="ct-list ct-list--nested">
            @if($tree->has($item->id))
                @include('admin.pages.category-tree.partials.tree-node', [
                    'items' => $tree->get($item->id),
                    'tree'  => $tree,
                ])
            @endif
        </ol>
    </li>
@endforeach
