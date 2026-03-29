# Reference Examples

## Purpose
This document contains short examples extracted from the Laravel reference project. They illustrate the coding style and architectural patterns that AI assistants should reproduce.

## Controller Example
Thin admin web controller using typed arguments, `FormRequest` validation, and redirect responses.

```php
class PageController extends Controller
{
    public function show(Page $page): View
    {
        return view('admin::pages.page.show', compact('page'));
    }

    public function update(Page $page, UpdateOrCreatePageRequest $request): RedirectResponse
    {
        $page->update($request->validated());

        return back()->with('success', trans('system.flash_message_updated', ['resource' => 'Page']));
    }
}
```

## FormRequest Example
Validation is kept in dedicated request classes, including multilingual array fields.

```php
class UpdateOrCreatePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|array',
            'title.*' => 'required|max:255',
            'slug' => 'required|array',
            'slug.*' => 'required|max:255',
            'description' => 'required|array',
            'description.*' => 'required|max:100000',
            'is_active' => 'required|boolean',
        ];
    }
}
```

## Service Class Example
Services hold reusable domain logic such as building and transforming hierarchical data.

```php
class CategoryService
{
    public function getCategoryTreeAsArray(Shop $shop): array
    {
        $tree = $shop->category;

        if (!$tree) {
            return [];
        }

        $tree['childrens'] = $this->buildCategoryTree($shop->category, $shop);

        return ['categories' => $this->transformTree($tree)];
    }
}
```

## Eloquent Model Example
Models are rich and define relationships, accessors, scopes, and fillable fields.

```php
class Category extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'parent_id',
        'description',
        'is_active',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function childrens(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id', 'id')
            ->where('is_active', true);
    }

    public function getLinkAttribute(): string
    {
        return LaravelLocalization::getLocalizedURL(app()->getLocale(), $this->slug);
    }
}
```

## Blade Template Example
Admin list pages are usually Blade-first and enhanced with AJAX DataTables.

```blade
<table id="blogs-dt" class="table dt-responsive nowrap">
    <thead>
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Updated</th>
        <th>Action</th>
    </tr>
    </thead>
</table>

@push('script')
<script>
    $(function () {
        $('#blogs-dt').DataTable({
            ajax: '{{ route('admin.datatable.blogs', ['shop' => $shop]) }}',
            columns: [
                {name: 'id', data: 'blog_id'},
                {name: 'title', data: 'blog_title'},
            ],
        });
    });
</script>
@endpush
```

## Vue Component Registration Example
Vue is mounted into Blade pages as progressive enhancement, not as a full SPA shell.

```js
import { createApp } from 'vue';
import store from './store';
import Catalog from './../vue/pages/Catalog.vue';
import Product from './../vue/pages/Product.vue';

const app = createApp({});

app.component('catalog', Catalog);
app.component('product', Product);

app.use(store);
app.mount('#page');
```

## Blade + Vue Integration Example
Blade layouts render the shell and inject Vue components directly into the page.

```blade
<div id="page" class="page">
    @include('shop::layout.components.header')

    <modal-compare
        :in18="{{ TransHelper::in18('modal-compare') }}"
        :text_link_route="'{{ route('page.compare') }}'"
    ></modal-compare>

    @yield('content-wrapper')

    @include('shop::layout.components.footer')
</div>
```

## Declarative Routes Example
The reference project does not describe routes one by one in isolation. It groups them declaratively by context, prefix, middleware, controller, and route-name namespace.

### Localized Shop Web Routes
```php
Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localizationRedirect', 'localeViewPath'],
], function () {
    Route::controller(PageController::class)->group(function () {
        Route::group(['as' => 'page.'], function () {
            Route::get(LaravelLocalization::transRoute('shop-urls.about'), 'about')->name('about');
            Route::get(LaravelLocalization::transRoute('shop-urls.contact'), 'contact')->name('contact');
            Route::get('compare', 'compare')->name('compare');
        });
    });
});
```

### Declarative API Group
```php
Route::group([
    'as' => 'api.',
    'prefix' => 'api',
], function () {
    Route::controller(CartController::class)->group(function () {
        Route::group([
            'prefix' => 'cart',
            'as' => 'cart.',
        ], function () {
            Route::get('{action?}', 'getCart')->name('get');
            Route::post('payment/{paymentMethod}', 'updatePaymentMethod')->name('update.payment-method');
            Route::post('shipping/{shippingMethod}', 'updateShippingMethod')->name('update.shipping-method');
        });
    });
});
```

### Declarative Admin CRUD Group
```php
Route::group([
    'as' => 'admin.',
    'prefix' => 'adm',
], function () {
    Route::group([
        'middleware' => ['auth:admin', 'admin.has_permission'],
    ], function () {
        Route::controller(CategoryController::class)->group(function () {
            Route::group(['as' => 'category.', 'prefix' => 'categories'], function () {
                Route::get('', 'index')->name('index');
                Route::get('{category}', 'show')->name('show');
                Route::put('{category}', 'update')->name('update');
            });
        });
    });
});
```

## Library Examples By Concern
### Sliders
- `slick-carousel` powers shop sliders and product galleries.
- Vue and jQuery wrappers initialize the same slider behavior from Blade-driven pages.

### Translations
- `mcamara/laravel-localization` is used for locale prefixes and translated route segments.
- `spatie/laravel-translatable` is used for multilingual model attributes.
- Blade translations rely on `trans()` and `__()`.

### Logging
- `bugsnag/bugsnag-laravel` is used for error monitoring.
- `opcodesio/log-viewer` is used for browsing Laravel logs.
- standard Laravel logging remains available for application logs.

## Source Files
These examples were derived from the reference project:
- `reference/laravel-10/app/Http/Controllers/Admin/Web/Page/PageController.php`
- `reference/laravel-10/app/Http/Requests/Admin/Web/Page/UpdateOrCreatePageRequest.php`
- `reference/laravel-10/app/Services/Shop/Api/Category/CategoryService.php`
- `reference/laravel-10/app/Models/Category/Category.php`
- `reference/laravel-10/resources/admin/views/pages/blog/data-table/table.blade.php`
- `reference/laravel-10/resources/shop/assets/js/vue.js`
- `reference/laravel-10/resources/shop/views/layout/main.blade.php`
- `reference/laravel-10/routes/web.php`
- `reference/laravel-10/routes/api.php`
