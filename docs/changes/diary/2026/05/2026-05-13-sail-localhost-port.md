# Дневник — 2026-05-13

## 12:30 (Europe/Kyiv) chore[config.sail,documentation.readme] — Sail: приложение на 80-хост и APP_URL без порта

**Entry ID:** 01JSAIL80420260513  
**Agent:** Composer  
**Дата:** 2026-05-13  
**Ветка:** development  

### Файлы
- `docker-compose.yml` (+новый в корне)
- `tmp/docker-compose.yml`
- `.env.example`
- `README.md`

### Что сделано
Добавлен корневой `docker-compose.yml` для `./vendor/bin/sail` (тот же набор сервисов, что во временном каталоге). В `.env.example` и README зафиксировано правило совпадения `APP_URL` и `APP_PORT`: для адреса `http://localhost` без суффикса порта использовать `APP_PORT=80`, перезапуск Sail; указан вариант `laravel.test` через `/etc/hosts`. В README описано, что делать если порт 80 занят (оставить `:8080` и выровнять `APP_URL`).

### Почему
При работе через `localhost:8080` с `APP_URL=http://localhost` ломались генерация URL и отображение ресурсов.

### Влияние
- **БД/API:** N/A  
- **Инфра:** стандартный запуск через compose из корня репозитория  

### Проверено
- Локально синтаксис YAML визуально; запуск Sail — у разработчика после правки своего `.env`.

### Follow-up
- [ ] В своём `.env` выставить `APP_PORT=80` и `APP_URL=http://localhost`, затем `sail down` / `sail up -d`.

## 22:00 (Europe/Kyiv) chore[config.sail,documentation.readme] — один compose-файл: убран docker-compose.yml, предупреждение Sail

**Entry ID:** 01JCOMPOSEONLY20260513  
**Agent:** Composer  
**Дата:** 2026-05-13  
**Ветка:** development  

### Файлы
- `compose.yaml` (+комментарий в шапке)
- `docker-compose.yml` (удалён)
- `README.md`
- `tmp/docker-compose.yml` (синхронизирован с compose.yaml)

### Что сделано
Docker Compose v2 сообщал: «Found multiple config files» (`compose.yaml` и `docker-compose.yml`). Удалён дубликат `docker-compose.yml`; источник правды — **`compose.yaml`** (именно его Sail использовал по умолчанию). В README описано назначение `compose.yaml`. Содержимое `tmp/docker-compose.yml` выровняно под `compose.yaml`.

### Почему
Убрать предупреждения и непредсказуемость (какой файл реально подхватили).

### Влияние
- Инфра: один файл конфигурации Compose в корне.

### Проверено
- N/A (`sail …` локально после `git pull`).

### Follow-up
- [ ] N/A
