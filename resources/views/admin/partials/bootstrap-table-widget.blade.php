@props([
    'tableId' => 'bootstrap-table',
    'dataUrl' => '',
    'pageSize' => 10,
    'pagination' => true,
    'search' => true,
    'columns' => [],
    'actionsFormatter' => null,
])

@pushOnce('head', 'bootstrap-table-css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-table@1.22.1/dist/bootstrap-table.min.css" rel="stylesheet">
@endPushOnce

<table
    id="{{ $tableId }}"
    data-toggle="table"
    data-url="{{ $dataUrl }}"
    data-pagination="{{ $pagination ? 'true' : 'false' }}"
    data-side-pagination="server"
    data-page-size="{{ $pageSize }}"
    data-page-list="[10, 25, 50, 100]"
    data-search="{{ $search ? 'true' : 'false' }}"
    data-show-refresh="true"
    class="table table-striped table-bordered"
>
    <thead>
        <tr>
            @foreach ($columns as $column)
                <th
                    data-field="{{ $column['field'] }}"
                    @if (! empty($column['sortable']))
                        data-sortable="true"
                    @endif
                >
                    {{ $column['title'] }}
                </th>
            @endforeach
            @if ($actionsFormatter !== null && $actionsFormatter !== '')
                <th data-field="actions" data-formatter="{{ $actionsFormatter }}">Actions</th>
            @endif
        </tr>
    </thead>
</table>

@pushOnce('scripts', 'bootstrap-table-cdn')
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-table@1.22.1/dist/bootstrap-table.min.js"></script>
    @vite(['resources/themes/admin/assets/js/admin-bootstrap-table.js'])
@endPushOnce
