# Дневник изменений — 2026-05-12

## 20:15 (Europe/Kyiv) feat[model.banner,trait.attachable,routes.admin-web,routes.admin-api,controller.admin.banner] — CRUD баннеров с изображением (FilePond, bootstrap-table)

**Entry ID:** 01JBANNERCRUD20260512  
**Agent:** Composer  
**Дата:** 2026-05-12  
**Ветка:** development  

### Файлы
- `app/Models/Concerns/Attachable.php` (+новый)
- `app/Models/Banner.php` (+новый)
- `database/migrations/2026_05_12_120000_create_banners_table.php` (+новый)
- `database/factories/BannerFactory.php` (+новый)
- `app/Services/Admin/BannerListingService.php` (+новый)
- `app/Services/Admin/BannerAttachmentService.php` (+новый)
- `app/Http/Resources/Admin/BannerResource.php` (+новый)
- `app/Http/Controllers/Admin/Api/BannerTableDataController.php` (+новый)
- `app/Http/Controllers/Admin/BannerController.php` (+новый)
- `app/Http/Requests/Admin/Banner/StoreBannerRequest.php` (+новый)
- `app/Http/Requests/Admin/Banner/UpdateBannerRequest.php` (+новый)
- `routes/admin-web.php`
- `routes/admin-api.php`
- `app/View/Composers/AdminLayoutComposer.php`
- `resources/views/admin/partials/sidebar.blade.php`
- `resources/views/admin/pages/banners/view.blade.php`, `create.blade.php`, `edit.blade.php` (+новые)
- `public/admin/images/no-image.jpg` (+placeholder)

### Что сделано
Табличный список баннеров на bootstrap-table (колонки id, code, preview, title, active, даты действий с модальным подтверждением удаления записи через `adminBootstrapTableDelete`). Полноценные страницы create/edit с загрузкой картинки через FilePond (CDN npm/jsdelivr): файлы сохраняются на диск `public`, в списке и при отсутствии файла показывается `public/admin/images/no-image.jpg`. Отдельный `DELETE` для снятия изображения с ответом JSON и выводом результата в `adminNotify`. Трейт `Attachable` и поле `image_path`, мягкое удаление, иерархия `parent_id` с защитой от циклов и от удаления родителя с детьми.

### Почему
Реализация по ТЗ из `banner_promt.md` в стиле модуля Static pages.

### Влияние
- **БД:** таблица `banners` (parent_id, code, title, sort_no, is_active, image_path, timestamps, soft deletes).
- **API:** `GET admin/api/banners/table` для bootstrap-table; `DELETE admin/banners/{banner}/image`.
- **Производительность:** N/A.

### Проверено
- Тесты: N/A (отдельных тестов на баннеры нет).
- Миграции: `./vendor/bin/sail artisan migrate --no-interaction --force` — ok.
- Линтер: ok (`./vendor/bin/pint --dirty`).

### Follow-up
- [ ] При необходимости добавить feature-тесты на CRUD/удаление изображения.

## 20:55 (Europe/Kyiv) feat[views.admin] — Превью выбранного в FilePond изображения в форме баннера

**Entry ID:** 01JBANPREVIEW20260512  
**Agent:** Composer  
**Дата:** 2026-05-12  
**Ветка:** development  

### Файлы
- `resources/views/admin/pages/banners/create.blade.php`
- `resources/views/admin/pages/banners/edit.blade.php`

### Что сделано
Добавлен блок «Image preview» с `<img>`; при выборе или drop файла в FilePond превью сразу показывает выбранное изображение через `URL.createObjectURL`, при снятии файла в блоке восстанавливается сохранённый URL или плейсхолдер `no-image`. На странице редактирования кнопка удаления файла на сервере перенесена рядом с превью; при отсутствии сохранённого изображения в превью отображается плейсхолдер.

### Почему
Чтобы оператор видел результат выбора изображения до отправки формы.

### Влияние
- **БД:** N/A
- **API:** N/A
- **Производительность:** N/A

### Проверено
- Тесты: N/A
- Линтер: N/A (Blade)

### Follow-up
- [ ] N/A

## 21:05 (Europe/Kyiv) fix[views.admin] — FilePond: файл в multipart-форме (save баннер + превью)

**Entry ID:** 01JBANSTOREFILE20260512  
**Agent:** Composer  
**Дата:** 2026-05-12  
**Ветка:** development  

### Файлы
- `resources/views/admin/pages/banners/create.blade.php`
- `resources/views/admin/pages/banners/edit.blade.php`

### Что сделано
В конфиг FilePond добавлены `storeAsFile: true` и `instantUpload: false`, имя поля явно задаётся из `input` (`banner_image`). Без этого FilePond не передавал файл в обычной POST-сборке формы — `image_path` не сохранялся, везде оставался плейсхолдер.

### Почему
После сохранения картинка не отображалась в таблице и на edit.

### Влияние
- **БД/API:** только корректная загрузка существующих форм  
- **Производительность:** N/A

### Проверено
- Логика соответствует документации FilePond (`storeAsFile`, `instantUpload`).

### Follow-up
- [ ] Если потребуется поддержка очень старых Safari без DataTransfer/File в `storeAsFile`, понадобится отдельный upload через API или fallback без FilePond.

## 21:20 (Europe/Kyiv) fix[trait.attachable] — URL превью баннеров без привязки к APP_URL (Sail/Docker)

**Entry ID:** 01JBANTHUMBREL20260512  
**Agent:** Composer  
**Дата:** 2026-05-12  
**Ветка:** development  

### Файлы
- `app/Models/Concerns/Attachable.php`

### Что сделано
Для файлов на диске `public` возвращается путь того же хоста, что у страницы: `/storage/<относительный путь>` вместо `Storage::url()` на базе `APP_URL`. Так превью в таблице и на edit не запрашивают «чужой» базовый URL из `.env` (типично в Sail: `localhost` vs имя контейнера/порт). Добавлена защита от `..` в пути из БД. Проверен `sail artisan storage:link` — ссылка `public/storage` уже существовала.

### Почему
В браузере отображалась «битая» картинка при наличии файла.

### Влияние
- **БД/API:** только формат выдачи URL клиенту.
- **Производительность:** N/A

### Проверено
- Pint.

### Follow-up
- [ ] При установке приложения в подкаталог (не корень домена) может понадобиться префикс URL из конфига отдельно.
