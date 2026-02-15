# Academic Domain Specification

Version 1.0

This document defines the Academic domain boundary, entities, relationships, and design decisions. No migrations or code should be written until this spec is approved.

---

## 1. Domain Boundary

### 1.1 What Academic Owns

- **CourseCategory** — Classification of courses (e.g. UG, PG, Diploma). No hardcoded categories.
- **Course** — A program/degree (name, duration, intake, eligibility, etc.).
- **Specialization** — A track or focus within a course (e.g. specializations under an M.Tech).
- **Syllabus** — Versioned syllabus document (PDF) linked to a course (and optionally to a specialization later).

### 1.2 What Academic Does NOT Own

- **Students** — Student domain (enrollment, records, documents).
- **Exam sessions / Results / Grades** — Examination domain.
- **Enrollments, intake-year, semesters, credits, GPA** — Out of scope for this domain.

Academic is **pure structure**: it describes what is offered, not who is enrolled or how they are assessed.

---

## 2. Entities

### 2.1 CourseCategory

| Attribute     | Type           | Constraints        | Notes                    |
|--------------|----------------|--------------------|--------------------------|
| id           | bigint         | PK, auto-increment |                          |
| name         | string(255)    | required           | Display name             |
| slug         | string(255)    | required, unique   | URL-friendly identifier  |
| description  | text           | nullable           |                          |
| is_active    | boolean        | default true       | Visibility / publish     |
| sort_order   | integer        | default 0          | Display order            |
| timestamps   |                |                    | created_at, updated_at    |

**Rules:** Slug is unique. Categories are managed by admin; public only sees `is_active = true`.

---

### 2.2 Course

| Attribute     | Type           | Constraints        | Notes                    |
|--------------|----------------|--------------------|--------------------------|
| id           | bigint         | PK, auto-increment |                          |
| course_category_id | bigint  | FK, required       | Belongs to one category  |
| name         | string(255)    | required           |                          |
| slug         | string(255)    | required, unique   | Per-instance unique       |
| duration     | string(100)    | nullable           | e.g. "2 Years", "4 Years"|
| intake       | integer        | nullable           | Seats / capacity         |
| eligibility  | text           | nullable           | Free text                 |
| description  | text           | nullable           |                          |
| is_active    | boolean        | default true       | Visibility                |
| sort_order   | integer        | default 0          | Order within category     |
| timestamps   |                |                    | created_at, updated_at     |

**Rules:** One category per course. Slug unique globally (not per-category). Public sees only `is_active = true` courses.

---

### 2.3 Specialization

| Attribute        | Type           | Constraints        | Notes                    |
|-----------------|----------------|--------------------|--------------------------|
| id              | bigint         | PK, auto-increment |                          |
| course_id       | bigint         | FK, required       | Belongs to one course    |
| name            | string(255)    | required           |                          |
| slug            | string(255)    | required           | Unique within course     |
| description     | text           | nullable           |                          |
| industry_relevance | text        | nullable           | Free text                 |
| career_outcomes | text           | nullable           | Free text                 |
| is_active       | boolean        | default true       | Visibility                |
| sort_order      | integer        | default 0          | Order within course      |
| timestamps      |                |                    | created_at, updated_at    |

**Rules:** Many specializations per course. Slug unique per course (composite: course_id + slug). Public sees only `is_active = true` specializations.

---

### 2.4 Syllabus

| Attribute     | Type           | Constraints        | Notes                    |
|--------------|----------------|--------------------|--------------------------|
| id           | bigint         | PK, auto-increment |                          |
| course_id    | bigint         | FK, required       | Belongs to one course    |
| specialization_id | bigint     | FK, nullable       | Optional; for later      |
| academic_year| string(20)     | nullable           | e.g. "2024-25" (optional)|
| version      | string(50)     | nullable           | e.g. "v1", "2024"        |
| file_path    | string(500)    | required           | Path on storage (public)  |
| is_active    | boolean        | default true       | Which version is “current”|
| published_at | datetime       | nullable           | When made active         |
| timestamps   |                |                    | created_at, updated_at    |

**Rules:** At least one of course_id or (future) specialization_id. File stored on public disk (e.g. `syllabus/`). Only one “current” syllabus per course (or per specialization) can be enforced in app logic if needed; DB allows multiple with `is_active = true` for simplicity unless we add a unique constraint later.

---

## 3. Relationships

```
CourseCategory 1 ----< N Course
Course 1 ----< N Specialization
Course 1 ----< N Syllabus
(Optional later: Specialization 1 ----< N Syllabus)
```

- **CourseCategory** has many **Course**s.
- **Course** has many **Specialization**s.
- **Course** has many **Syllabus**es (versioned documents).
- **Syllabus** belongs to **Course**; `specialization_id` reserved for future use (syllabus per specialization).

No many-to-many. No cross-domain relations (no student_id, no exam_session_id).

---

## 3.1 Delete Strategy (FK Behavior)

- **Decision:** Use FK `RESTRICT`. No cascading delete.
- **Rule:** Deleting a category that has courses (or a course that has specializations/syllabi) must **fail** until the admin deletes children first.
- **Implementation:** In migrations, define foreign keys without `cascadeOnDelete()`. Laravel default is RESTRICT. No cascading delete; admin must remove dependent records first.

---

## 3.2 Public Visibility Invariant

- **Rule:** A course is visible publicly **only if** both `category.is_active = true` **and** `course.is_active = true`.
- **Rule:** Public visibility condition: `category.is_active = true AND course.is_active = true` for listing and showing courses. An inactive category hides all its courses regardless of `course.is_active`.
- Specializations and syllabi on a course detail page are shown only when their own `is_active = true`; the course itself is already gated by the category+course invariant above.

---

## 3.3 Syllabus Activation Invariant

- **Rule:** For each course, **at most one** syllabus has `is_active = true` at any time (domain rule; enforced in application when activating a syllabus).
- **Implementation:** When setting a syllabus to `is_active = true`, set all other syllabi for the same course (and optionally same specialization) to `is_active = false`. Enforce in Filament form/action or model observer. No DB unique constraint required.

---

## 3.4 URL / Slug Strategy

- **Decision:** Slug is **editable** and **auto-generated** from name on create (live from name, user can override). No lock-after-create.
- **Spec wording:** Slug: auto-generated from name; manually editable. No immutability guarantee; old URLs may 404 if slug is changed.

---

## 3.5 Ordering Behavior

- **Rule:** When ordering by `sort_order`, use a **deterministic** secondary sort: `orderBy('sort_order')->orderBy('name', 'asc')` so that ties in sort_order do not produce inconsistent list order across requests.
- **Spec wording:** Default ordering for all Academic entities: sort_order ASC, then name ASC.

---

## 4. Index Strategy

| Table            | Index / Unique key                          | Purpose                    |
|-----------------|---------------------------------------------|----------------------------|
| course_categories | UNIQUE(slug)                               | Lookup by slug, public URLs|
| course_categories | INDEX(is_active), INDEX(sort_order)        | Listing, ordering          |
| courses         | FK(course_category_id)                     | By category                |
| courses         | UNIQUE(slug)                                | Lookup, public URLs        |
| courses         | INDEX(is_active), INDEX(sort_order)        | Listing, ordering          |
| specializations | FK(course_id)                               | By course                  |
| specializations | UNIQUE(course_id, slug)                     | Unique slug per course     |
| specializations | INDEX(is_active), INDEX(sort_order)        | Listing, ordering          |
| syllabi         | FK(course_id), FK(specialization_id)        | By course / specialization |
| syllabi         | INDEX(is_active), INDEX(course_id, is_active) | Current syllabus per course |

No composite indexes beyond the ones above unless profiling shows need.

---

## 5. Soft Deletes

- **Do not use soft deletes** for Academic domain tables.

Reason: Structure is reference data. Hard delete is acceptable; admin can re-create. Soft deletes add complexity and query burden without a strong audit requirement for “deleted” categories/courses. If audit is needed later, use activity logs instead.

---

## 6. Publish / Visibility Logic (is_active)

| Entity         | is_active meaning        | Public behavior              | Admin behavior        |
|----------------|--------------------------|-----------------------------|------------------------|
| CourseCategory | Category is visible      | List/show only is_active=true | CRUD all, see all   |
| Course         | Course is visible        | List/show only is_active=true | CRUD all, see all   |
| Specialization | Specialization visible   | List/show only is_active=true | CRUD all, see all   |
| Syllabus       | Version is “current”     | Download only is_active=true  | CRUD all, see all   |

- **Public**: Read-only; every query filters `is_active = true`.
- **Admin**: Full CRUD; can set is_active to false (hide) or true (publish). No separate “draft” table; is_active is the single flag.
- No scheduled publish/unpublish in this spec; can be added later if required.

---

## 7. Permissions (RBAC)

Align with existing pattern. Permissions:

| Permission pattern     | Scope                          |
|-----------------------|---------------------------------|
| category.view         | View categories (admin)        |
| category.create       | Create category                |
| category.update       | Edit category                  |
| category.delete       | Delete category                |
| course.view           | View courses (admin)           |
| course.create         | Create course                  |
| course.update         | Edit course                    |
| course.delete         | Delete course                  |
| specialization.view   | View specializations (admin)   |
| specialization.create | Create specialization         |
| specialization.update | Edit specialization            |
| specialization.delete | Delete specialization          |
| syllabus.view         | View syllabus (admin)          |
| syllabus.create       | Upload syllabus                |
| syllabus.update       | Edit syllabus record           |
| syllabus.delete       | Delete syllabus                |

**Role assignment (existing + new):**

- **Super Admin**: All (already via Gate::before).
- **Academic Admin**: category.*, course.*, specialization.*, syllabus.* (and existing page.view, announcement.* as per current seeder).
- **Content Manager / Examination / Placement / Student**: No new Academic permissions unless specified elsewhere.

---

## 8. Public Surface (Read-Only)

Public routes (no auth required) must only expose active data:

1. **View categories** — List course categories where `is_active = true`, e.g. `/academic` or `/courses`.
2. **View courses in category** — List courses in a category where category and course are active, e.g. `/academic/category/{slug}` or `/courses/{category_slug}`.
3. **View course details** — Single course by slug; show course + its active specializations + active syllabi, e.g. `/academic/courses/{slug}`.
4. **View specializations** — As part of course detail; no standalone “all specializations” required unless product asks.
5. **Download syllabus** — Serve file for an active syllabus; track download count only if required later (not in initial scope).

All public queries MUST filter by `is_active = true`. No bypass.

---

## 9. Admin Surface (Filament)

- **CourseCategoryResource** — CRUD categories (name, slug, description, is_active, sort_order).
- **CourseResource** — CRUD courses; category select; slug from name; is_active, sort_order.
- **SpecializationResource** — CRUD specializations; course select; slug; is_active, sort_order. Optionally relation manager on CourseResource.
- **SyllabusResource** — CRUD syllabus records; course (and optional specialization) select; file upload (PDF); version; academic_year; is_active; published_at.

Navigation group: **Academic**. All gated by the permissions above.

---

## 10. Future Extension Space

Reserved or optional in schema/design:

- **Syllabus.specialization_id** — Already nullable; when needed, “syllabus per specialization” without new tables.
- **Academic year / intake year** — Not modeled now; add when admission or exam integration requires it.
- **Semesters, credits, GPA** — Explicitly out of scope; belong to Examination or Student domain if ever.
- **Download tracking** — Optional later; add a `download_count` or event log if product needs it.
- **Multi-tenancy / institute_id** — Not in scope; add only if product demands multiple institutes in one app.

No schema fields for “future might need” unless listed above; avoid speculative columns.

---

## 11. Summary

| Item              | Decision                                      |
|-------------------|-----------------------------------------------|
| Entities          | CourseCategory, Course, Specialization, Syllabus |
| Relationships     | Category 1:N Course; Course 1:N Specialization, 1:N Syllabus |
| Delete strategy   | FK RESTRICT; no cascade; admin deletes children first |
| Public visibility | category.is_active AND course.is_active for courses |
| Syllabus current  | At most one is_active = true per course; enforce in app |
| Slug              | Auto-generated, editable; no immutability guarantee |
| Ordering          | sort_order ASC, then name ASC (deterministic) |
| Indexes           | FKs, UNIQUE(slug) or UNIQUE(course_id, slug), is_active/sort_order where needed |
| Soft deletes      | No                                            |
| Publish logic     | is_active only; public reads is_active = true |
| Permissions       | category.*, course.*, specialization.*, syllabus.* |
| Public             | Read-only; categories, courses, course detail, syllabus download |
| Admin              | Filament resources; permission-gated          |
| Future             | specialization_id on Syllabus; no semesters/credits/GPA in Academic |

This spec is the single source of truth for the Academic domain. Migrations and code must follow it.
