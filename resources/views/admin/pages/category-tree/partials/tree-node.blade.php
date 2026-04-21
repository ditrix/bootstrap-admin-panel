@foreach($items as $item)
    <li class="ct-item" data-id="{{ $item->id }}">
        <div class="ct-node">
            <span class="ct-handle" title="{{ __('Drag to reorder') }}">☰</span>
            <span class="ct-id text-muted">{{ $item->id }}</span>
            <span class="ct-title">{{ $item->title }}</span>
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
