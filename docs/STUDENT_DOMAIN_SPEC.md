# Student Domain Specification

Version 1.0

This document defines the Student domain boundary, entities, relationships, and design decisions. This spec is approved and locked. Implementations must follow it.

---

## 1. Domain Boundary

### 1.1 What Student Owns

- **Student (profile)** — Links a **User** (auth) to institutional identity: enrollment ID, roll number, course assignment, contact info, verification status.
- **Student documents** — Uploaded documents (e.g. photo, ID proof, marksheets) with type and optional verification state.
- **Verification workflow** — State machine for student record (e.g. pending / verified / rejected) and who can transition it.

### 1.2 What Student Does NOT Own

- **Courses, categories, syllabi** — Academic domain (Student references Course only via FK).
- **Exam sessions, results, grades** — Examination domain (will reference Student later).
- **Placements, recruiters** — Placement domain.
- **Authentication** — User (login, password) remains in Core; Student is a **profile** tied to one User.

Student is the **enrollment and identity layer**: who is enrolled, in which course, with what documents and verification state. Examination will depend on Student (e.g. “student X in course Y appeared in exam Z”).

---

## 2. Dependency Position

Intended layering:

```
Core (auth, users, roles)
  → CMS (pages, announcements)
  → Academic (categories, courses, specializations, syllabi)
  → Student (enrollment, roll number, course, documents, verification)
  → Examination (sessions, results) — depends on Student
  → Placement (recruiters, placements)
```

Student **depends on** Academic (Course). Examination **depends on** Student. Do not build Examination before Student is stable.

---

## 3. Entities (Proposed)

### 3.1 Student

A single **student profile** per User. One User can have at most one Student record (1:1 with User when role is Student).

| Attribute       | Type           | Constraints        | Notes                          |
|----------------|----------------|--------------------|--------------------------------|
| id             | bigint         | PK, auto-increment |                                |
| user_id        | bigint         | FK, required, unique | Links to users; one student per user |
| course_id      | bigint         | FK, required       | Belongs to Academic.Course     |
| enrollment_id  | string(255)    | required, unique   | Institute enrollment number    |
| roll_number    | string(255)    | required           | UNIQUE(course_id, roll_number)       |
| phone          | string(50)     | nullable           | Contact                        |
| alternate_phone| string(50)     | nullable           | Optional contact               |
| verification_status | string(50) | required, default pending | e.g. pending, verified, rejected |
| verified_at    | datetime       | nullable           | When status became verified    |
| verified_by    | bigint         | FK users, nullable  | Who verified (admin user_id)   |
| notes          | text           | nullable           | Admin notes (e.g. rejection reason) |
| timestamps     |                |                    | created_at, updated_at         |

**Rules:**

- One User → one Student (enforced by unique user_id).
- Student belongs to one Course (Academic); course_id required.
- enrollment_id unique globally.
- roll_number unique per course: UNIQUE(course_id, roll_number).
- verification_status: enum-like string `pending` | `verified` | `rejected` (no boolean).

---

### 3.2 Student Document

Uploaded documents tied to a student (photo, id_proof, marksheet, etc.).

| Attribute     | Type           | Constraints        | Notes                    |
|--------------|----------------|--------------------|--------------------------|
| id           | bigint         | PK, auto-increment |                          |
| student_id   | bigint         | FK, required       | Belongs to Student        |
| type         | string(100)    | required           | e.g. photo, id_proof, marksheet_10, marksheet_12 |
| file_path    | string(500)    | required           | Path on storage (e.g. private disk) |
| original_name| string(255)    | nullable           | User’s file name         |
| verified     | boolean        | default false      | Admin verified this doc? |
| verified_at  | datetime       | nullable           | When verified            |
| timestamps   |                |                    | created_at, updated_at   |

**Rules:**

- Many documents per Student.
- type: enum or constrained set TBD (photo, id_proof, marksheet_10, marksheet_12, other).
- Store on private disk by default; serve via signed URL or controller so only authorized users see files.

---

## 4. Relationships

```
User 1 ---- 1 Student
Student N ----< 1 Course (Academic)
Student 1 ----< N StudentDocument
```

- **Student** belongs to **User** (user_id); one-to-one (unique user_id).
- **Student** belongs to **Course** (Academic); many students per course.
- **Student** has many **StudentDocument**s.

No many-to-many in this domain. Examination will later add relations like “Student has many ExamRegistrations” or “Result belongs to Student”; those stay in Examination spec.

---

## 5. Design Decisions to Lock (Before Implementation)

These should be agreed and then written into this spec as numbered subsections (like Academic 3.1–3.5).

### 5.1 Delete Strategy (FK Behavior)

- **Proposal:** RESTRICT on all FKs. Deleting a User who has a Student must fail (or require “remove student first”). Deleting a Course that has Students must fail until students are reassigned or removed. No cascading delete.
- **Open:** Should “delete student” soft-delete or hard-delete? If hard, what happens to documents and future exam/result references?

### 5.2 Roll Number Uniqueness

- **Proposal:** Unique per course: (course_id, roll_number) unique. Same roll number can exist in different courses.
- **Alternative:** Globally unique roll_number. To be decided.

### 5.3 Verification Workflow

- **Proposal:** Simple status: `pending` | `verified` | `rejected`. Only users with `student.verify` (e.g. Examination Cell or Academic Admin) can set verified/rejected. verified_at and verified_by stored for audit.
- **Open:** Notifications (email on verify/reject)? Out of scope for v1 unless required.

### 5.4 Document Storage and Access

- **Proposal:** Private disk (e.g. `student-documents/`). Download only via controller that checks auth + permission (student.view or student’s own record). No public URLs.
- **Open:** Max file size, allowed MIME types, virus scan — to be defined.

### 5.5 Who Can Create Students?

- **Proposal:** Users with `student.create` (e.g. Academic Admin / Examination Cell) create Student records and link to an existing User (or create User then Student). Student role users do not self-register as “students” in this profile sense unless product explicitly wants self-registration.
- **Open:** Self-registration flow (e.g. “apply for admission”) vs admin-only creation. Recommendation: admin-only for v1.

### 5.6 Public vs Admin Surface

- **Proposal:** No public routes for Student data. All access via admin (Filament) with permissions student.view, student.create, student.update, student.verify. Student users may get a “my profile” view later (same app, authenticated); not in initial scope.
- **Open:** Student dashboard “view my documents / status” — add when product asks.

---

## 6. Index Strategy

| Table           | Index / Unique key              | Purpose                |
|----------------|----------------------------------|------------------------|
| students       | UNIQUE(user_id)                 | One student per user   |
| students       | UNIQUE(enrollment_id)           | Lookup by enrollment   |
| students       | UNIQUE(course_id, roll_number)                                 | Per-course roll uniqueness          |
| students       | FK(course_id), FK(user_id)      | Relations, filters    |
| students       | INDEX(verification_status)      | Filter pending/verified |
| student_documents | FK(student_id)                | By student             |
| student_documents | INDEX(student_id, type)       | By student and type   |

---

## 7. Permissions (RBAC)

Already defined in seeders:

- student.view
- student.create
- student.update
- student.verify

**Proposal:**

- **Examination Cell** (or designated role): student.view, student.verify, and optionally student.create/update for their workflow.
- **Academic Admin**: optionally student.view, student.create, student.update (assign to course, create profile).
- **Student** (role): no admin permissions; may get “view own profile” later.

Exact role assignment to be aligned with current RoleAndPermissionSeeder and product.

---

## 8. Admin Surface (Filament) — Proposed

- **StudentResource** — List students (name via user, enrollment_id, roll_number, course, verification_status). Filters: course, verification_status. Create: select User (or create user), select Course, enrollment_id, roll_number, contact, verification_status. Edit: same + documents relation manager. Actions: “Verify” / “Reject” for pending students (gated by student.verify).
- **StudentDocument** — Either relation manager on StudentResource, or separate resource with student_id. File upload (private disk), type dropdown, verified toggle.

Navigation group: **Student** (or **Academic** if you want students under same group; recommend separate **Student** group).

---

## 9. Future Extension Space

- **Intake year / admission batch** — Add when Examination or reporting needs “students of 2024 intake.”
- **Specialization** — Optional FK from Student to Academic.Specialization if “student in M.C.A. with specialization in AI” is required.
- **Guardian / address** — Add fields or separate table when product requires.
- **Self-service** — Student dashboard: view profile, upload documents, see verification status. Out of scope for v1.

No speculative columns; add when a concrete requirement exists.

---

## 10. Summary

| Item             | Decision                                                |
|------------------|---------------------------------------------------------|
| Entities         | Student (profile), StudentDocument                     |
| User link        | Student 1:1 User (user_id unique)                     |
| Course link      | Student N:1 Course (Academic)                          |
| enrollment_id    | Globally unique                                        |
| roll_number      | Unique per course (UNIQUE(course_id, roll_number))     |
| verification     | pending / verified / rejected (string, not boolean)     |
| Documents        | StudentDocument: type, file_path (private disk), verified |
| Delete strategy  | FK RESTRICT; no cascade; delete docs before student    |
| Document storage | Private disk; serve via controller; no public URLs      |
| Student creation | Admin-only (Phase 1)                                   |
| Self profile UI  | Not in Phase 1                                         |
| Permissions      | student.view, student.create, student.update, student.verify |
| Admin            | Filament StudentResource (+ documents); no public routes |
| Dependencies     | Student depends on Academic (Course). Examination depends on Student. |

---

## 11. Implementation

Migrations, models, factories, RBAC (policies + seeder), Filament StudentResource with documents RelationManager, authenticated document download route, and tests must follow this spec. Same discipline as Academic domain.
