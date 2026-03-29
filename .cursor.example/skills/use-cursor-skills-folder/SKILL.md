---
name: use-cursor-skills-folder
description: Applies project standards from `.cursor/skills/` (STACK, ARCHITECTURE, PATTERNS, EXAMPLES, SKILLS, ADMIN_PANEL_PATTERN). Use when writing or changing code in this repo, when the user mentions project skills, stack, patterns, architecture, or admin panel conventions.
---

# Навыки из `.cursor/skills/`

## Назначение

В корне репозитория папка **`.cursor/skills/`** — источник правды по стеку, архитектуре и паттернам. Его нужно читать **до** нетривиальных правок в коде, если задача касается Laravel, админки, API или фронта в стиле проекта.

Путь от корня проекта: `.cursor/skills/` (не путать с `~/.cursor/skills` — это личные навыки Cursor).

## Какие файлы открывать

| Файл | Когда читать |
|------|----------------|
| [STACK.md](../STACK.md) | Стек, Vite, Blade/Vue-острова, зависимости |
| [ARCHITECTURE.md](../ARCHITECTURE.md) | Слои, поток запроса, где жить логике |
| [PATTERNS.md](../PATTERNS.md) | Tree-CRUD, `parent_id`/`position`, DnD, мультиязычные поля |
| [EXAMPLES.md](../EXAMPLES.md) | Примеры структуры кода под проект |
| [SKILLS.md](../SKILLS.md) | Слои (Controller, FormRequest, Service, Model), соглашения по именованию |
| [ADMIN_PANEL_PATTERN.md](../ADMIN_PANEL_PATTERN.md) | Админ-панель, макеты, списки, формы |

Читай **только то**, что относится к текущей задаче (не обязательно все файлы сразу).

## Порядок работы

1. По типу задачи выбери 1–3 файла из таблицы выше.
2. Прочитай их через инструмент чтения файлов (учитывай `.cursorignore`).
3. Соблюдай описанные там паттерны; при конфликте с общими правилами Cursor — **приоритет у явных правил этого репозитория** (в т.ч. `.cursor/rules/`).
4. Если в `.cursor/skills/` позже появятся подпапки с собственными `SKILL.md`, относись к ним как к дополнительным узким навыкам и подключай по смыслу задачи.

## Не дублировать

Не копируй длинные выдержки из этих файлов в ответ пользователю без нужды: держи в голове выводы и применяй в коде.
