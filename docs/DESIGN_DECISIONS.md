# Design Decisions & Future-Proofing

This document records intentional decisions and checkpoints for auth, frontend, security, and scalability. Not all items are implemented yet; they are locked so future work does not drift.

---

## 1. Auth Entry Points (Current vs Future)

**Current (implemented):**
- **Central login** at GET/POST `/login` (Breeze-style form). Single entry for all users.
- After login: **Student** (only role) → `/student`; all other roles → `/admin` (Filament). Uses `redirect()->intended($default)` so deep links (e.g. guest visited `/admin` or `/student`) are restored after login.
- **Filament** has no own login page; guests visiting `/admin` are redirected to `/login` via custom middleware.
- Logout redirects to `/login`.
- **Student dashboard** at GET `/student` (auth + Student role only; non-Students redirected to `/admin`).

**Future semantics (when adding faculty):**
- `/login` → central login (unchanged).
- `/admin` → admin dashboard (Filament).
- `/student` → student dashboard (implemented).
- `/faculty` or similar → faculty dashboard (if/when built); extend login redirect logic then.

---

## 2. Layout & Styling Intent

**Current state:**
- **Public:** Instrument Sans, public layout (brand-facing).
- **App (dashboard/profile):** Figtree, app layout.
- **Admin:** Filament theme (internal tool).

**Intentional choice:** Decide whether the post-login dashboard is part of the **brand** or part of the **system**.
- If brand → unify fonts (e.g. Instrument Sans across public + app).
- If internal tool → current divergence is acceptable; avoid accidental inconsistency elsewhere.

Document the choice here when decided so styling stays consistent by design.

---

## 3. Results Search Security

**Context:** `/results/search` is public. Academic portals are heavily scraped.

**Locked requirements (implement before or soon after production use):**
- **Rate limiting** on the search route (per IP / per session).
- **Validation hardening** (input length, format; no raw query in error messages).
- **No detailed error leaks** (generic messages to user; log details server-side).
- **CAPTCHA** (or similar) when scaling or when abuse appears — evaluate when needed.

Current implementation: ensure validation and safe error handling; add rate limiting and CAPTCHA as next hardening steps.

---

## 4. Long-Term Scalability (API & Backend Shape)

**Context:** Today the app is Blade-rendered and admin is Filament. Future possibilities: mobile app, API integrations, student mobile portal.

**Foresight (no overengineering now):**
- **API layer separation** when a client (mobile, third-party) appears: versioned routes, API resources, auth (e.g. Sanctum).
- **Service classes** for domain logic instead of fat controllers — adopt as features grow so the same logic can serve web and API.
- **Keep backend clean** so adding an API layer later does not require rewriting controllers.

No need to build API or services prematurely; avoid putting logic in controllers that will be hard to reuse from an API later.

---

## 5. Lock Before Building More UI

Before expanding examination, student, or other modules, lock:

| Item | Status / Notes |
|------|----------------|
| Role matrix | Document which roles exist and what they can do (see RBAC seeders). |
| Module–permission mapping | Which permissions gate which modules (Academic, Student, Examination, Placement, CMS). |
| Modules requiring future student login | e.g. "My results", "My documents", "My placements" — list and plan auth for them. |
| `/login` as central entry vs redirect | Decide when student/faculty dashboards are planned. |

Once these are explicit, auth and permission refactors stay under control.

---

## 6. Structural Principles (Audit Reference)

- **Layout separation:** Public vs app vs admin — clear and appropriate.
- **Auth structure:** Single Filament login by design; path to central login documented.
- **Stack:** Blade + Tailwind + Alpine + Filament — appropriate, no overengineering.
- **Future extensibility:** Document API and service foresight; implement when a concrete need (mobile, integration) appears.

Avoid adding complexity while the system is stable. Lock decisions above, then build.

---

## 7. Content Architecture: What Is “Dynamic” vs “System”

**Goal:** No brand or institutional copy hardcoded. System UI labels remain controlled.

**Do not** aim for “no static content” by pushing every string into the CMS. That leads to key-value chaos, broken UIs from accidental edits, and an overwhelming admin. Structure beats maximal dynamism.

### Three categories of text

| Category | Meaning | Where it lives |
|----------|--------|----------------|
| **Content** | Must be editable by institute (brand, tone, empty states). | CMS Pages or site settings. |
| **Configurable copy** | Maybe editable (e.g. footer, ticker label, section titles). | Site settings (structured). |
| **System labels** | Should **not** live in CMS (field names, table headers, actions). | Translation files (`lang/`) or code. |

### What MUST be dynamic (CMS / settings)

- **Brand / marketing:** Home cards (About, Notices, Director’s Message titles and descriptions), section headings (e.g. “Our Recruiters”), placement intro, results intro, footer copyright line, announcement ticker label.
- **Empty states (public):** “No announcements at the moment”, “No course categories available”, “No placement data”, etc. Institutes change tone; make them editable.
- **Back labels:** “Back to Notices”, “Back to Courses” — safe to make configurable.

### What must NOT be CMS-editable (system labels)

Keep these out of the database CMS. Use `lang/` translation files if flexibility is needed:

- Marks, Subject, Total, Percentage, Grade, Passed, Failed.
- Duration, Eligibility, Intake (field labels).
- Search (button), “Roll number or Enrollment ID” (and similar form labels).
- Table headers and status words that define UI structure.

Reason: these are system/field labels. CMS is not a dictionary. Let translations handle terminology and locale.

### Borderline (structured only)

- Nav labels, “LPA”, currency symbol, “Highest Package (CTC / Stipend)”, title suffixes.
- If nav is dynamic, use a **menu table** (e.g. `label`, `route`, `order`, `is_visible`), not loose key/value strings. Do not store route names inside free-text fields.

### Recommended content architecture

| Layer | Use for | Not for |
|-------|---------|--------|
| **Site settings table** (key/value or JSON) | Footer text, ticker label, currency/LPA, empty-state defaults, global section titles. | Nav structure, long copy, system labels. |
| **CMS Pages** (existing) | Home content, results intro, placement intro, academic overview, any long-form institutional copy. | Field labels, nav items, one-line config. |
| **Menu table** | Navigation: label, route, order, visibility. Structured. | Marketing paragraphs, system terms. |
| **Translation files** (`lang/en/*.php`) | System field labels, buttons, table headers, status words (Marks, Subject, Passed, etc.). | Brand taglines, footer legal, empty-state marketing copy. |
| **Code** | Business logic text, validation messages that must stay in sync with code. | User-facing marketing or configurable tone. |

### Final rule (quick reference)

| Type | Where it lives |
|------|-----------------|
| Marketing / institutional copy | CMS Pages |
| Global configurable phrases | Site settings |
| Navigation structure | Menu table |
| System field labels | Translation files |
| Business logic text | Code (not editable) |

Do not turn the CMS into a key-value graveyard. Classify by type first; then choose the layer. Scalable CMS stays disciplined.
