# Content Architecture Implementation Plan

## Current state

- **CMS Pages:** [app/Models/Page.php](app/Models/Page.php) and [database/seeders/PageSeeder.php](database/seeders/PageSeeder.php) provide `home`, `about`, `director-message`, `vision-mission`. [resources/views/public/home.blade.php](resources/views/public/home.blade.php) uses the home page body but **card titles and descriptions are hardcoded** (e.g. "About Us", "Notices", "Director's Message").
- **No site settings:** No table or model for key/value configurable copy.
- **No translations:** No `lang/` directory; all system labels (Marks, Subject, Search, Duration, etc.) are hardcoded in Blade.
- **Nav:** Hardcoded in [resources/views/components/public/header.blade.php](resources/views/components/public/header.blade.php) (Home, About, Courses, Notices, Results, Placements + auth links).

Classification of existing static strings:

| Layer | Examples (from codebase) |
|-------|---------------------------|
| **Site settings** | Footer line, ticker label "Notices:", empty states ("No announcements at the moment", "No course categories available", "No recruiters to display", "No placement data...", "Back to Notices", "Back to Courses"), section titles ("Our Recruiters", "Placement Statistics", "Check Results", "Highest Package (CTC / Stipend)", "Placements by Academic Year/Course"), results intro sentence, currency/LPA display (₹, LPA). |
| **CMS Pages** | Home card title + description for About, Notices, Director's Message; optional results/placement intro paragraphs if long-form. |
| **Translations** | Subject, Marks, Total, Percentage, Grade, Passed, Failed, Absent, Duration, Eligibility, Intake, Search, "Roll number or Enrollment ID", Academic Year, Total Placements, Course, Name, Roll No, Download PDF, Visit website, Specializations, Syllabus, etc. |
| **Code** | Validation messages, business logic text. |

---

## Architecture (matches DESIGN_DECISIONS.md §7)

- **Site settings:** Key/value store (DB) for footer, ticker label, empty-state messages, back labels, global section titles, currency symbol, LPA label. Cached, seeded with defaults.
- **CMS Pages:** Existing. Use for home card content (pull title + excerpt from Page by slug) and, if desired, dedicated intro pages (e.g. results, placement).
- **Menu (optional):** Structured table (`label`, `route`, `order`, `is_visible`). Only if nav should be admin-editable; otherwise keep nav in Blade and use translations for label text.
- **Translations:** `lang/en/*.php` for all system field/table labels and buttons. Views use `__('key')`.

---

## Implementation plan

### 1. Site settings table and model

- **Migration:** `site_settings` table: `id`, `key` (string, unique), `value` (text), `timestamps`.
- **Model:** `SiteSetting` with static/cacheable getter; keys are predefined.
- **Seeder:** Seed default values for every key used in the app.
- **Admin:** Filament page or Resource for editing predefined keys only.

### 2. Use site settings in views

- Footer, ticker, empty states, back labels, section titles, currency/LPA, results/placement intros.

### 3. Drive home cards from CMS Pages

- Home cards from `Page` by slug (`about`, `director-message`, `notices`). Add `notices` page to seeder.

### 4. Translation files for system labels

- Create `lang/en/*.php`; replace system labels in Blade with `__()`.

### 5. Menu table (optional / Phase 2)

- Migration `menus`, Model `Menu`, Filament resource; header reads from menu if implemented.

### 6. Testing and validation

- Feature test for site settings; run existing tests and Pint.

---

## Order of work (recommended)

1. Migration + `SiteSetting` model + seeder with all keys and defaults.
2. Helper or facade to read settings (with optional cache).
3. Wire footer, ticker, empty states, back labels, section titles, currency/LPA in views to site settings.
4. Home cards: load About and Director's Message from Pages; add Notices page for the third card.
5. Create `lang/en/*.php` and replace system labels in Blade with `__()`.
6. (Optional) Menu table + model + Filament + header from menu.
7. Filament UI for site settings.
8. Tests + Pint.

---

## Out of scope (by design)

- Putting system field labels (Marks, Subject, Search, etc.) in the CMS or site settings.
- Free-form key/value CMS; all setting keys are predefined.
- Storing route names or structural data in free-text fields; if menu is dynamic, use the structured menu table only.
