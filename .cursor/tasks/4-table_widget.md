TASK: implement table widget for crud functionality
STEPS:
1 Create crud functionality StaticPage (Model, Controllers, Resource, Resources, routes). it will bt used for static page
fields [id, parent_id(default 0), code, title, description, content, sort_no, slug, is_active, timestamt]
2 prepare testing datas for StaticPages

3 migrate from existing Simple-DataTables to the Bootstrap5 - table
3.1 implement functonal Simple-DataTables by using bootstrep widget and components
3.2 configurable ability in blade pages (php and js options). buttons [show,edit,delete] configured on the blade
example:
<table id="pet-table"
       data-toggle="table"
       data-url="{{ route('admin.pets.index') }}"   <!-- Laravel возвращает JSON -->
       data-pagination="true"
       data-search="true"
       data-sortable="true"
       data-page-size="10">
    <thead>
        <tr>
            <th data-field="id" data-sortable="true">ID</th>
            <th data-field="name">TITLE</th>
            <!-- другие поля -->
            <th data-field="actions" data-formatter="actionsFormatter">Actions</th>
        </tr>
    </thead>
</table>
``js
function actionsFormatter(value, row) {
    return `
        <a href="/admin/pets/${row.id}" class="btn btn-sm btn-info">show</a>
        <a href="/admin/pets/${row.id}/edit" class="btn btn-sm btn-warning">edit</a>
        <button onclick="deletePet(${row.id})" class="btn btn-sm btn-danger">remove</button>
    `;
}
``js
3.3 styles and commont js store in the ./resources/thems/admin/assets/css  ./resources/thems/admin/assets/ja
also you can used @push(..) @andpush

3.4 Create view.blade for Static pages using model StaticPage usen new componenet bootstrap-table 



RULES:
1 use ./cursor/rules  and ./cursor/skills
2 for differant steps use different agent GPT-5.3 Codex low Fast
3 after complete step agent gave to provide tests and write information and results to the file workflow.md
4 after complete task write documentation. 

NOTES:
current project configured for sail
***