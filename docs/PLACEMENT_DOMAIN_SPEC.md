# Placement Domain Specification

Version 1.0

This document defines the Placement domain boundary, entities, relationships, workflows, and design decisions. This spec is approved and locked. Implementations must follow it.

---

## 1. Domain Boundary

### 1.1 What Placement Owns

* **Recruiter** — A company that recruits students; includes primary contact and public visibility controls.
* **Placement** — A record of a student receiving an offer from a recruiter in a given academic year; includes offer status and compensation.
* **Placement statistics (derived)** — Aggregated counts per academic year or per course (no separate stats table in Phase 1).

Placement is responsible for managing recruiter data and student placement outcomes.

---

### 1.2 What Placement Does NOT Own

* **Students** — Owned by Student domain.
* **Courses / Subjects** — Owned by Academic domain.
* **Exam results** — Owned by Examination domain.
* **Admission or application workflows** — Not part of Placement.
* **Placement analytics dashboards** — Out of scope for Phase 1.

Placement depends on Student (mandatory) and indirectly on Academic (via Student → Course relationship). No other domain depends on Placement.

---

## 2. Dependency Position

Intended layering:

```
Core (auth, users, roles)
  → CMS (pages, announcements)
  → Academic (categories, courses, specializations, syllabi, subjects)
  → Student (enrollment, roll number, course, verification)
  → Examination (sessions, forms, marks, results)
  → Placement (recruiters, student placements)
```

Placement is the last domain in the current system chain.

Examination does not depend on Placement. Placement is terminal.

---

## 3. Entities

### 3.1 Recruiter

Represents a company recruiting students.

| Attribute     | Type    | Constraints        | Notes                   |
| ------------- | ------- | ------------------ | ----------------------- |
| id            | bigint  | PK, auto-increment |                         |
| name          | string  | required           | Company name            |
| slug          | string  | required, unique   | URL-friendly identifier |
| website       | string  | nullable           |                         |
| logo_path     | string  | nullable           | Stored on public disk   |
| contact_name  | string  | nullable           | Primary contact         |
| contact_email | string  | nullable           |                         |
| contact_phone | string  | nullable           |                         |
| is_active     | boolean | default true       | Public visibility       |
| sort_order    | integer | default 0          | Display ordering        |
| timestamps    |         |                    | created_at, updated_at  |

**Rules:**

* slug is unique globally.
* Public listing shows only `is_active = true`.
* Recruiter cannot be deleted if Placement records exist (FK RESTRICT).

---

### 3.2 Placement

Represents one student offer from one recruiter in a given academic year.

| Attribute      | Type          | Constraints             | Notes                       |
| -------------- | ------------- | ----------------------- | --------------------------- |
| id             | bigint        | PK, auto-increment      |                             |
| student_id     | bigint        | FK students, RESTRICT   |                             |
| recruiter_id   | bigint        | FK recruiters, RESTRICT |                             |
| academic_year  | string        | required                | e.g. "2025-2026"            |
| placement_type | string        | required                | job, internship             |
| designation    | string        | nullable                | Role/title                  |
| package_amount | decimal(10,2) | nullable                | CTC or stipend              |
| status         | string        | required                | offered, joined, declined   |
| offer_date     | date          | nullable                |                             |
| timestamps     |               |                         | created_at, updated_at      |

**Uniqueness:**

* UNIQUE(student_id, recruiter_id, academic_year)

Prevents duplicate offers for the same student–company–year combination.

**Rules:**

* Student must exist.
* Recruiter must exist.
* Student cannot be deleted if Placement exists (FK RESTRICT).
* Recruiter cannot be deleted if Placement exists (FK RESTRICT).

---

## 4. Relationships

```
Recruiter 1 ----< N Placement >---- 1 Student
```

* A **Recruiter** has many **Placements**.
* A **Student** has many **Placements**.
* Placement belongs to one Recruiter and one Student.

Course is accessed indirectly via Student → Course when needed for reporting.

No many-to-many tables required.

---

## 5. Workflows

### 5.1 Placement Status Workflow

```
offered → joined
offered → declined
```

* **offered**: Offer extended but not confirmed.
* **joined**: Student accepted and joined.
* **declined**: Offer rejected.

Status transitions are one-way. No backward movement.

---

### 5.2 Season Close / Publish Concept (Phase 1 Minimal)

There is no separate PlacementSeason entity in Phase 1.

However:

* Once placements for an academic year are considered "finalized", admin policy may restrict deletion.
* Policy-level restriction: Prevent delete if placement status = joined AND academic_year is finalized (implementation-level rule; spec reserves this behavior).

---

## 6. Design Decisions (Locked)

| Decision              | Choice                                                       |
| --------------------- | ------------------------------------------------------------ |
| Recruiter structure   | Company + single primary contact                             |
| Placement row meaning | One student offer per recruiter per academic year            |
| Uniqueness            | UNIQUE(student_id, recruiter_id, academic_year)              |
| Status tracking       | offered / joined / declined                                  |
| Compensation storage  | Yes (package_amount decimal)                                 |
| Delete strategy       | FK RESTRICT everywhere                                       |
| Who can create        | PlacementCell only                                           |
| Public surface        | Recruiters + placement statistics (no student personal data) |
| Permissions           | recruiter.* and placement.* (including delete)               |

No placement analytics engine. No batch season entity in Phase 1.

---

## 7. Index Strategy

| Table      | Index / Unique key                              | Purpose                |
| ---------- | ----------------------------------------------- | ---------------------- |
| recruiters | UNIQUE(slug)                                    | Public URL lookup      |
| recruiters | INDEX(is_active), INDEX(sort_order)             | Listing                |
| placements | UNIQUE(student_id, recruiter_id, academic_year) | Prevent duplicates     |
| placements | FK(student_id), FK(recruiter_id)                | Relationship filtering |
| placements | INDEX(academic_year)                            | Year-based reporting   |
| placements | INDEX(status)                                   | Filter offered/joined  |

No additional composite indexes unless profiling demands.

---

## 8. Permissions (RBAC)

### Recruiter Permissions

* recruiter.view
* recruiter.create
* recruiter.update
* recruiter.delete

### Placement Permissions

* placement.view
* placement.create
* placement.update
* placement.delete

### Role Assignments

* **PlacementCell**: all recruiter.* and placement.*
* **AcademicAdmin**: no placement permissions (kept clean)
* **SuperAdmin**: all permissions

Deletion is allowed but may be restricted by policy when academic year is finalized (implementation-level rule).

---

## 9. Admin Surface (Filament)

Navigation group: **Placement**

### RecruiterResource

* CRUD recruiter records
* Upload logo (public disk)
* Toggle is_active
* Sort order
* Permission-gated via recruiter.*

### PlacementResource

* Select student (searchable)
* Select recruiter (searchable)
* Academic year
* Placement type
* Designation
* Package amount
* Status
* Offer date

Filters:

* Academic year
* Status
* Recruiter
* Course (via student relationship)

Permission-gated via placement.*

---

## 10. Public Surface (Phase 1)

### Public Pages

1. **Recruiters Page**

   * List active recruiters
   * Logo + website
   * Ordered by sort_order

2. **Placement Statistics (Derived)**

   * Total placements per academic year
   * Total placements per course
   * Highest package (optional derived)
   * No student names exposed

Public queries must use `is_active = true` recruiters only.

No public listing of individual student placements in Phase 1.

---

## 11. Test Strategy

* Recruiter uniqueness (slug).
* Placement uniqueness (student_id, recruiter_id, academic_year).
* FK RESTRICT:

  * Cannot delete Recruiter with placements.
  * Cannot delete Student with placements.
* Placement status workflow (offered → joined/declined only).
* RBAC:

  * PlacementCell full access.
  * Student role forbidden.
* Public:

  * Only active recruiters visible.
  * Placement stats reflect only valid placement rows.

---

## 12. Summary

| Item            | Decision                                            |
| --------------- | --------------------------------------------------- |
| Entities        | Recruiter, Placement                                |
| Relationships   | Recruiter 1:N Placement; Student 1:N Placement      |
| Status          | offered → joined/declined                           |
| Compensation    | Stored as decimal                                   |
| Delete strategy | FK RESTRICT everywhere                              |
| Permissions     | recruiter.* and placement.*                         |
| Public          | Recruiters + aggregated stats only                  |
| Dependencies    | Student (mandatory), Academic (indirect via course) |

---
