# Документация: Drag & Drop дерево (Kohana → Laravel)

Описание реализации древовидного представления данных с поддержкой перемещения узлов
через drag-and-drop. Источник: модуль `Documents` на фреймворке Kohana (Wezom CMS).

---

## 1. Структура базы данных

Таблица: `documents_tree`

| Поле        | Тип         | Описание                                      |
|-------------|-------------|-----------------------------------------------|
| `id`        | INT PK AI   | Первичный ключ                                |
| `parent_id` | INT         | ID родительского узла, `0` — корневой элемент |
| `sort`      | INT         | Порядок сортировки (последовательный счётчик) |
| `status`    | TINYINT     | Статус (0/1)                                  |
| `title`     | VARCHAR     | Заголовок (мультиязычный)                     |
| `text`      | TEXT        | Контент (мультиязычный)                       |

Ключевые колонки для дерева: **`parent_id`** (вложенность) и **`sort`** (порядок).

---

## 2. Слой данных (Model)

Файл: `src/Wezom/Modules/Documents/Models/Documents.php`

```php
class Documents extends CommonI18n
{
    public static $table = 'documents_tree';
    // ...
}
```

`CommonI18n` — базовый класс, предоставляет методы:

- `getRows(NULL, 'sort', 'ASC')` — выбирает все строки, сортируя по полю `sort`
- `getRow($id)` — одна запись по ID
- `insert($data)` / `update($data, $id)` / `delete($id)`

---

## 3. Контроллер (Controller)

Файл: `src/Wezom/Modules/Documents/Controllers/Documents.php`

### indexAction — формирование дерева

```php
function indexAction()
{
    // 1. Загружаем все строки, отсортированные по sort ASC
    $result = Model::getRows(NULL, 'sort', 'ASC');

    // 2. Группируем по parent_id — ключ массива = parent_id
    $arr = array();
    foreach ($result AS $obj) {
        $arr[$obj->parent_id][] = $obj;
    }

    // 3. Передаём во view: $arr[0] — корневые узлы,
    //    $arr[$id] — дочерние узлы элемента с id=$id
    $this->_content = View::tpl(
        array(
            'result'    => $arr,
            'tablename' => Model::$table,  // нужен для AJAX-сохранения
        ),
        'Documents_tree/Index'
    );
}
```

Важно: во view передаётся `tablename` (имя таблицы), которое потом прокидывается в
`data-table` аттрибут скрытого элемента. AJAX-обработчик сортировки универсальный —
он получает имя таблицы динамически.

---

## 4. HTML-разметка

### Index.php

```php
<div class="dd pageList" id="myNest">
    <ol class="dd-list">
        <?php echo View::tpl(
            array('result' => $result, 'cur' => 0),
            'Documents_tree/Menu'
        ); ?>
    </ol>
</div>

<!-- Хранит имя таблицы для AJAX -->
<span id="parameters" data-table="<?php echo $tablename; ?>"></span>

<!-- Скрытый input: сюда записывается JSON дерева перед AJAX-запросом -->
<input type="hidden" id="myNestJson">
```

### Menu.php — рекурсивный шаблон

```php
<?php if (isset($result[$cur]) AND count($result[$cur])): ?>
    <?php if ($cur > 0): ?><ol><?php endif ?>

    <?php foreach ($result[$cur] as $obj): ?>
        <!-- data-id — обязателен для сериализации nestable -->
        <li class="dd-item dd3-item" data-id="<?php echo $obj->id; ?>">

            <!-- Область захвата (drag handle) — именно за неё тянут мышью -->
            <div title="Переместить строку" class="dd-handle dd3-handle">Drag</div>

            <!-- Контент строки -->
            <div class="dd3-content">
                <table>
                    <tr>
                        <td><!-- drag column --></td>
                        <td><label><input type="checkbox"/></label></td>
                        <td>
                            <a href="/wezom/documents/edit/<?php echo $obj->id; ?>">
                                <?php echo $obj->title; ?>
                            </a>
                        </td>
                        <td><!-- status widget --></td>
                        <td><!-- action menu --></td>
                    </tr>
                </table>
            </div>

            <!-- Рекурсивный вызов для дочерних узлов ($cur = id текущего элемента) -->
            <?php echo View::tpl(
                array('result' => $result, 'cur' => $obj->id),
                'Documents_tree/Menu'
            ); ?>
        </li>
    <?php endforeach; ?>

    <?php if ($cur > 0): ?></ol><?php endif ?>
<?php endif ?>
```

**Принцип рекурсии:**
- `$cur = 0` → корневые узлы (parent_id = 0)
- При рекурсивном вызове `$cur = $obj->id` → дочерние узлы этого элемента
- `$result` — весь массив, уже сгруппированный по `parent_id`

**HTML-структура результата:**
```html
<div class="dd pageList" id="myNest">
  <ol class="dd-list">
    <li class="dd-item dd3-item" data-id="1">
      <div class="dd-handle dd3-handle">Drag</div>
      <div class="dd3-content">...</div>
      <ol>
        <li class="dd-item dd3-item" data-id="3">
          <div class="dd-handle dd3-handle">Drag</div>
          <div class="dd3-content">...</div>
        </li>
      </ol>
    </li>
    <li class="dd-item dd3-item" data-id="2">
      <div class="dd-handle dd3-handle">Drag</div>
      <div class="dd3-content">...</div>
    </li>
  </ol>
</div>
```

---

## 5. JavaScript: jQuery Nestable

Файл: `src/Wezom/Media/js/jquery.nestable.min.js`
Автор: David Bushell — http://dbushell.com/ (MIT/BSD лицензия)

### Конфигурация (defaults)

```javascript
var defaults = {
    listNodeName    : 'ol',          // тег списка
    itemNodeName    : 'li',          // тег элемента
    rootClass       : 'dd',          // класс корневого контейнера
    listClass       : 'dd-list',     // класс списка
    itemClass       : 'dd-item',     // класс элемента
    dragClass       : 'dd-dragel',   // класс клона при перетаскивании
    handleClass     : 'dd-handle',   // класс ручки захвата
    collapsedClass  : 'dd-collapsed',
    placeClass      : 'dd-placeholder', // класс-заглушка на месте перетаскиваемого
    noDragClass     : 'dd-nodrag',
    emptyClass      : 'dd-empty',
    maxDepth        : 5,             // максимальная глубина вложенности
    threshold       : 20             // порог (px) для изменения уровня
};
```

### Инициализация плагина

```javascript
// Подключить nestable к контейнеру #myNest
// plugins.js, строки 704–710

var depth = parseInt($('#myNest').data('depth'));
if (!depth) { depth = 5; }

$("#myNest").not('.pageList-del').nestable({
    dragClass: 'pageList dd-dragel',
    itemClass: 'dd-item',
    group: 1,
    maxDepth: depth
}).on("change", mySortable);

// Инициализировать начальный JSON в скрытом поле
myUpdateOutput($("#myNest").data("output", $("#myNestJson")));
```

### Обработчик изменений + AJAX-сохранение

```javascript
// plugins.js, строки 672–710

// Сериализует дерево в JSON и записывает в скрытый input
var myUpdateOutput = function(e) {
    var list   = e.length ? e : $(e.target),
        output = list.data('output');
    if ($(e.target).length) {
        if (window.JSON) {
            output.val(window.JSON.stringify(list.nestable('serialize')));
        }
    }
};

// Вызывается после каждого drag-drop (событие "change")
var mySortable = function(e) {
    // Игнорируем клики по чекбоксам
    if (e.target.outerHTML == '<input type="checkbox">') {
        return;
    }

    myUpdateOutput(e);  // → записать JSON в #myNestJson

    var json  = $("#myNestJson").val();       // JSON дерева
    var table = $('#parameters').data('table'); // имя таблицы из data-table

    $.ajax({
        url:      '/wezom/ajax/sortable',
        type:     'POST',
        dataType: 'JSON',
        data: {
            json:  json,
            table: table
        },
        success: function(data) { /* ... */ }
    });
};
```

### Формат сериализации

Метод `nestable('serialize')` обходит DOM и возвращает массив объектов,
считывая `data-*` атрибуты с каждого `<li>`. Дочерние узлы попадают в поле `children`.

Пример JSON (передаётся на сервер):
```json
[
  { "id": 1, "children": [
      { "id": 3 },
      { "id": 4 }
  ]},
  { "id": 2 },
  { "id": 5 }
]
```

---

## 6. Механика перемещения узлов

### 6.1 Перемещение вверх/вниз (сортировка по вертикали)

Реализовано в методе `dragMove` плагина.

```javascript
// dragMove — вертикальный блок

// Определяем: курсор выше или ниже середины целевого элемента
var before = e.pageY < (this.pointEl.offset().top + this.pointEl.height() / 2);

if (before) {
    this.pointEl.before(this.placeEl);  // вставить заглушку ПЕРЕД элементом
} else {
    this.pointEl.after(this.placeEl);   // вставить заглушку ПОСЛЕ элемента
}
```

**Алгоритм:**
1. При движении мыши отслеживается элемент под курсором (`document.elementFromPoint`)
2. Если cursor.y < (top элемента + height/2) → placeholder вставляется перед ним
3. Иначе → placeholder вставляется после него
4. При отпускании кнопки мыши (`dragStop`) placeholder заменяется реальным элементом

### 6.2 Изменение уровня (движение влево/вправо)

Реализовано в блоке "move horizontal" метода `dragMove`:

```javascript
// dragMove — горизонтальный блок
// Срабатывает когда курсор движется преимущественно по X
// и расстояние по X превысило порог threshold (20px)

if (mouse.dirAx && mouse.distAxX >= opt.threshold) {
    mouse.distAxX = 0; // сброс счётчика для следующей фазы
    prev = this.placeEl.prev(opt.itemNodeName);

    // === ВПРАВО: увеличить уровень вложенности ===
    if (mouse.distX > 0 && prev.length && !prev.hasClass(opt.collapsedClass)) {
        list = prev.find(opt.listNodeName).last();
        depth = this.placeEl.parents(opt.listNodeName).length;

        if (depth + this.dragDepth <= opt.maxDepth) {
            if (!list.length) {
                // создать новый <ol> внутри предыдущего элемента
                list = $('<ol/>').addClass(opt.listClass);
                list.append(this.placeEl);
                prev.append(list);
                this.setParent(prev);    // добавить кнопки expand/collapse
            } else {
                // добавить в последний существующий подсписок
                list = prev.children(opt.listNodeName).last();
                list.append(this.placeEl);
            }
        }
    }

    // === ВЛЕВО: уменьшить уровень вложенности ===
    if (mouse.distX < 0) {
        next = this.placeEl.next(opt.itemNodeName);
        if (!next.length) {
            // у placeholder нет следующего сиблинга → можно выйти из родителя
            parent = this.placeEl.parent();
            this.placeEl.closest(opt.itemNodeName).after(this.placeEl);
            if (!parent.children().length) {
                // если список стал пустым → убрать кнопки expand/collapse
                this.unsetParent(parent.parent());
            }
        }
        // Если есть следующий сиблинг → нельзя подняться (дети остались бы без родителя)
    }
}
```

**Правило "влево"**: узел нельзя переместить на уровень выше, если у него есть
следующий сиблинг в текущем подсписке — дочерние элементы этого сиблинга потеряли бы
родителя.

**Правило "вправо"**: нельзя вложить в свёрнутый узел (`.dd-collapsed`).
Ограничение глубины: `depth + dragDepth <= maxDepth`.

### 6.3 Отслеживание оси движения

```javascript
// Вычисление текущей оси движения
var newAx = Math.abs(mouse.distX) > Math.abs(mouse.distY) ? 1 : 0;
// 1 = горизонталь, 0 = вертикаль

// dirAx хранит предыдущую ось
// Если ось сменилась — сбросить накопленное расстояние
if (mouse.dirAx !== newAx) {
    mouse.distAxX = 0;
    mouse.distAxY = 0;
}
mouse.dirAx = newAx;
```

Это предотвращает случайное изменение уровня при диагональном движении —
горизонтальное смещение должно накопиться до `threshold` (20px) **непрерывно**
вдоль одной оси.

---

## 7. AJAX-обработчик сортировки (сервер)

Файл: `src/Wezom/Modules/Ajax/Controllers/General.php`
URL: `POST /wezom/ajax/sortable`

```php
public function sortableAction()
{
    $table = Arr::get($this->post, 'table'); // имя таблицы
    $json  = Arr::get($this->post, 'json');  // JSON дерева от nestable
    $arr   = json_decode(stripslashes($json), true);

    // Рекурсивная функция сохранения
    function saveSort($arr, $table, $parentID, $i = 0)
    {
        foreach ($arr AS $a) {
            $inner = Common::checkField($table, 'parent_id'); // есть ли колонка parent_id?

            if ($inner) {
                // Таблица поддерживает иерархию
                $data = array('sort' => $i, 'parent_id' => $parentID);
            } else {
                // Плоская таблица — только сортировка
                $data = array('sort' => $i);
            }

            $id = Arr::get($a, 'id');
            Common::factory($table)->update($data, $id);
            $i++;

            $children = Arr::get($a, 'children', array());
            if (count($children)) {
                if (!$inner) {
                    // Плоская таблица: продолжить счётчик $i
                    $i = saveSort($children, $table, $id, $i);
                } else {
                    // Иерархическая: сбросить счётчик для каждого уровня
                    saveSort($children, $table, $id);
                }
            }
        }
        return $i;
    }

    saveSort($arr, $table, 0); // корневые узлы: parent_id = 0
    $this->success();
}
```

**Что происходит при сохранении:**
- Каждый узел получает новые значения `sort` (позиция в своём уровне) и `parent_id`
- Корневые узлы: `parent_id = 0`, `sort = 0, 1, 2, ...`
- Дочерние узлы: `parent_id = id родителя`, `sort = 0, 1, 2, ...` (независимый счётчик)
- Функция универсальная: работает с любой таблицей, если передать `table`

---

## 8. CSS

### nestable.css (базовые стили плагина)

```css
/* Список без маркеров */
.dd-list {
    display: block;
    list-style: none outside none;
    margin: 0;
    padding: 0;
    position: relative;
}

/* Вложенность — отступ слева */
.dd-list .dd-list {
    margin-left: 30px;
}

/* Скрыть свёрнутый список */
.dd-collapsed .dd-list {
    display: none;
}

/* Базовый элемент */
.dd-item, .dd-empty, .dd-placeholder {
    display: block;
    font-size: 13px;
    line-height: 20px;
    margin: 0;
    min-height: 20px;
    padding: 0;
    position: relative;
}

/* Стандартная ручка захвата */
.dd-handle {
    background: #fafafa;
    border: 1px solid #ccc;
    cursor: move;
    display: block;
    height: 30px;
    padding: 5px 10px;
}

/* Placeholder — пунктирный контур на месте перемещаемого элемента */
.dd-placeholder, .dd-empty {
    background: #ecf1f6;
    border: 1px dashed #4d7496;
    box-sizing: border-box;
    margin: 5px 0;
    min-height: 30px;
}

/* Клон элемента во время перетаскивания — поверх всего, без pointer-events */
.dd-dragel {
    pointer-events: none;
    position: absolute;
    z-index: 9999;
}
.dd-dragel .dd-handle {
    box-shadow: 2px 4px 6px 0 rgba(0, 0, 0, 0.1);
}

/* Режим dd3 (handle сбоку от контента) */
.dd3-content {
    display: block;
    height: 30px;
    padding: 5px 10px 5px 40px; /* отступ слева = место для handle */
}

/* Handle в режиме dd3 — абсолютно позиционирован слева от контента */
.dd3-handle {
    position: absolute;
    left: 0;
    top: 0;
    width: 30px;
    cursor: move;
    background: #ddd;
    border: 1px solid #aaa;
    text-indent: -9999px; /* спрятать текст */
}

/* Иконка ≡ на handle */
.dd3-handle:before {
    content: "≡";
    display: block;
    font-size: 20px;
    color: #fff;
    position: absolute;
    text-indent: 0;
    text-align: center;
    width: 100%;
    top: 3px;
}
```

### style.css (переопределения для .pageList)

```css
/* Контент строки — высота auto (под таблицу внутри) */
.pageList .dd3-content {
    background: #fff;
    border: 1px solid #ccc;
    height: auto;
    margin: 5px 0;
    padding: 5px 0 5px 28px;
}

/* Вложенность: переопределение отступа */
.pageList .dd-list .dd-list {
    margin-left: 48px;
}
.pageList.dd-dragel .dd-list {
    margin-left: 48px;
}

/* Handle — переопределён: без фона, пунктирная граница */
.pageList .dd3-handle {
    background: none;
    border: 1px dotted #aaa;
    cursor: move;
    left: 10px;
    top: 10px;
    position: absolute;
    width: 30px;
    text-indent: -9999px;
    line-height: 22px;
}

/* FontAwesome иконка на handle (f047 = стрелки в разные стороны) */
.pageList .dd-handle:before {
    color: #ccc;
    content: "\f047";
    display: block;
    font-family: FontAwesome;
    font-size: 17px;
    position: absolute;
    text-align: center;
    text-indent: 0;
    top: 3px;
    width: 100%;
}

/* Handle при перетаскивании — синий фон */
.pageList.dd-dragel .dd-handle {
    box-shadow: 2px 4px 6px 0 rgba(0, 0, 0, 0.1);
    background: #4d7496;
}
.pageList.dd-dragel .dd-handle:before {
    color: #fff;
}

/* Кнопки expand/collapse */
.pageList .dd-item > button {
    left: -50px;
    position: absolute;
    top: 8px;
}

/* Таблица внутри каждого dd3-content */
.pageList table {
    border-collapse: collapse;
    margin-bottom: 0;
}
.pageList table td {
    padding: 0 10px;
    border: 1px solid #ddd;
    border-top: 0;
    border-bottom: 0;
}
.pageList table tr td:first-child,
.pageList table tr td:last-child {
    border: 0;
}

/* Чередование цвета строк корневого уровня */
.pageList > ol > li:nth-child(odd) .dd3-content  { background: #f9f9f9; }
.pageList > ol > li:nth-child(even) .dd3-content { background: #fdfdfd; }

/* Отключить перетаскивание */
.drag-off .dd-handle {
    display: none;
}
```

---

## 9. Кнопки сворачивания/разворачивания

В HTML кнопки не присутствуют изначально. Плагин добавляет их программно
при инициализации через `setParent`:

```javascript
setParent: function(li)
{
    if (li.children(this.options.listNodeName).length) {
        li.prepend($(this.options.expandBtnHTML));
        li.prepend($(this.options.collapseBtnHTML));
    }
    li.children('[data-action="expand"]').hide();
}
```

HTML кнопок:
```html
<button data-action="expand"   class="bs-tooltip" title="Развернуть">Expand</button>
<button data-action="collapse" class="bs-tooltip" title="Свернуть">Collapse</button>
```

Кнопки управления всем деревом:
```javascript
$("[data-action=expand-all]").on("click",   function() { $('.dd').nestable('expandAll');  });
$("[data-action=collapse-all]").on("click", function() { $('.dd').nestable('collapseAll'); });
```

---

## 10. Поток данных: от drag-drop до БД

```
[Пользователь тянет узел]
        ↓
[jQuery Nestable отслеживает mousemove]
        ↓
[Определяет ось движения (X/Y)]
        ↓
[Вертикаль: переставляет placeholder до/после соседа]
[Горизонталь: вкладывает/вынимает placeholder из родителя]
        ↓
[mouseup → dragStop: placeholder заменяется реальным элементом]
        ↓
[Триггерится событие "change" на #myNest]
        ↓
[mySortable() → nestable('serialize') → JSON]
  Пример: [{"id":1,"children":[{"id":3},{"id":4}]},{"id":2}]
        ↓
[JSON записывается в <input id="myNestJson">]
        ↓
[AJAX POST /wezom/ajax/sortable]
  { json: "...", table: "documents_tree" }
        ↓
[sortableAction() рекурсивно обходит JSON]
        ↓
[UPDATE documents_tree SET sort=0, parent_id=0 WHERE id=1]
[UPDATE documents_tree SET sort=0, parent_id=1 WHERE id=3]
[UPDATE documents_tree SET sort=1, parent_id=1 WHERE id=4]
[UPDATE documents_tree SET sort=1, parent_id=0 WHERE id=2]
```

---

## 11. Подключение зависимостей

Порядок подключения скриптов (важен):

1. jQuery
2. `jquery.nestable.min.js` — сам плагин
3. `plugins.js` — инициализация и обработчики

CSS (порядок важен для переопределений):

1. `nestable.css` — базовые стили плагина
2. `style.css` — кастомные стили `.pageList`

---

## 12. Ограничения и особенности реализации

| Особенность | Описание |
|---|---|
| `maxDepth` | Задаётся в `data-depth` на `#myNest`, default = 5 |
| `threshold` | 20px горизонтального движения для смены уровня |
| Touch-поддержка | Плагин поддерживает touchstart/touchmove/touchend |
| `pointer-events: none` | На клоне при перетаскивании — иначе `elementFromPoint` не работает |
| Чекбоксы | В `mySortable` специально игнорируется событие от `<input type="checkbox">` |
| Универсальный AJAX | Один endpoint `/wezom/ajax/sortable` для всех деревьев; таблица передаётся параметром |
| Нет `parent_id` | Если в таблице нет колонки `parent_id` — сохраняется только плоская сортировка |

---

## 13. Рекомендации для реализации на Laravel

### Миграция

```php
Schema::create('documents_tree', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('parent_id')->default(0)->index();
    $table->integer('sort')->default(0)->index();
    $table->boolean('status')->default(1);
    $table->string('title')->nullable();
    $table->text('text')->nullable();
    $table->timestamps();
});
```

### Загрузка дерева в контроллере

```php
$rows = Document::orderBy('sort')->get();
$tree = $rows->groupBy('parent_id'); // аналог PHP foreach группировки
```

### AJAX-endpoint сортировки

```php
// POST /admin/ajax/sortable
public function sortable(Request $request)
{
    $table = $request->input('table');
    $arr   = json_decode($request->input('json'), true);

    $this->saveSort($arr, $table, 0);

    return response()->json(['success' => true]);
}

private function saveSort(array $arr, string $table, int $parentId, int $i = 0): int
{
    foreach ($arr as $item) {
        $hasParent = Schema::hasColumn($table, 'parent_id');
        $data = $hasParent
            ? ['sort' => $i, 'parent_id' => $parentId]
            : ['sort' => $i];

        DB::table($table)->where('id', $item['id'])->update($data);
        $i++;

        $children = $item['children'] ?? [];
        if (count($children)) {
            $i = $this->saveSort($children, $table, $item['id'], $hasParent ? 0 : $i);
        }
    }
    return $i;
}
```

### Blade-шаблон

```blade
<div class="dd pageList" id="myNest">
    <ol class="dd-list">
        @include('admin.tree.menu', ['result' => $tree, 'cur' => 0])
    </ol>
</div>
<span id="parameters" data-table="{{ $tablename }}"></span>
<input type="hidden" id="myNestJson">
```

```blade
{{-- resources/views/admin/tree/menu.blade.php --}}
@if(isset($result[$cur]) && $result[$cur]->count())
    @if($cur > 0)<ol>@endif

    @foreach($result[$cur] as $item)
        <li class="dd-item dd3-item" data-id="{{ $item->id }}">
            <div class="dd-handle dd3-handle" title="Переместить">Drag</div>
            <div class="dd3-content">
                {{-- содержимое строки --}}
                <a href="{{ route('admin.documents.edit', $item->id) }}">{{ $item->title }}</a>
            </div>
            @include('admin.tree.menu', ['result' => $result, 'cur' => $item->id])
        </li>
    @endforeach

    @if($cur > 0)</ol>@endif
@endif
```
