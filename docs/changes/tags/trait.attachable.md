# Изменения: trait.attachable

- 2026-05-12 — Трейт `App\Models\Concerns\Attachable` (диск/papка/колонка, URL превью, fallback thumb) для `Banner` → [../../diary/2026/05/2026-05-12-banners-crud.md#01JBANNERCRUD20260512]
- 2026-05-12 — `attachmentPublicUrl()`: префикс `/storage/…` без `APP_URL` (совместимость с Sail при рассогласовании хоста) → [../../diary/2026/05/2026-05-12-banners-crud.md#01JBANTHUMBREL20260512]
- 2026-05-14 — Плейсхолдер: `public/static/banners/no-image.jpg` вместо `public/admin/*` (конфликт с `/admin`) → [../../diary/2026/05/2026-05-12-banners-crud.md#01JBANNOPUBLICADMIN20260514]
- 2026-05-14 — Плейсхолдер: `resources/themes/admin/assets/img/no-image.jpg` через Vite `input` + `Vite::asset()` → [../../diary/2026/05/2026-05-12-banners-crud.md#01JBANIMGVITE20260514]
