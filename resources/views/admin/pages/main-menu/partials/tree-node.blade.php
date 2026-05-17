@foreach($items as $item)
    <li class="mm-item" data-id="{{ $item->id }}">
        <div class="mm-node">
            <span class="mm-handle" title="{{ __('Drag to reorder') }}">☰</span>
            <span class="mm-id text-muted">{{ $item->id }}</span>
            <span class="mm-title">{{ $item->title }}</span>
            <span class="mm-active" title="{{ __('Active') }}">
                @if($item->is_active)
                    <i class="dripicons-checkmark" style="color:green" aria-hidden="true"></i>
                @else
                    <i class="dripicons-cross" style="color:red" aria-hidden="true"></i>
                @endif
            </span>
            <span class="mm-node-actions">
                <a href="{{ route('admin.main-menu.edit', $item) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Edit') }}">
                    <i class="fas fa-edit"></i>
                </a>
                <button type="button" class="btn btn-sm btn-outline-danger mm-btn-delete" title="{{ __('Delete') }}"
                    onclick="adminBootstrapTableDelete(@js(route('admin.main-menu.destroy', $item)))">
                    <i class="fas fa-trash"></i>
                </button>
            </span>
        </div>
        <ol class="mm-list mm-list--nested">
            @if($tree->has($item->id))
                @include('admin.pages.main-menu.partials.tree-node', [
                    'items' => $tree->get($item->id),
                    'tree'  => $tree,
                ])
            @endif
        </ol>
    </li>
@endforeach
