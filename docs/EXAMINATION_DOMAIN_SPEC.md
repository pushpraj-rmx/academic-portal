# Examination Domain Specification

Version 1.0

This document defines the Examination domain boundary, entities, relationships, workflows, and design decisions. This spec is approved and locked. Implementations must follow it.

---

## 1. Domain Boundary

### 1.1 What Examination Owns

- **ExamSession** — Umbrella for an exam period (e.g. End-Semester May 2026): name, slug, academic year, session type, dates, status workflow, published_at.
- **ExamSessionSubject** — Links subjects (Academic) to a session; defines which subjects are examined and optional exam date/time.
- **ExamForm** — Registration of a student for an exam session; status workflow (applied / approved / rejected).
- **SubjectMark** — Individual marks per student per subject per session; absent flag; used to derive results dynamically.
- **Result calculation** — Derived on-the-fly from SubjectMark (no stored Result table); grade from config; pass/fail rules.

### 1.2 What Examination Does NOT Own

- **Subjects** — Academic domain (Examination references Subject via FK).
- **Students, courses** — Student and Academic domains (Examination references Student and Subject only).
- **Grade thresholds** — Stored in `config/exam.php`, not in database (no GradeRule entity).

Examination is the **workflow layer** for exam sessions, registration, marks entry, and result display. It depends on Academic (Subject, Course) and Student.

---

## 2. Dependency Position

Intended layering:

```
Core (auth, users, roles)
  → CMS (pages, announcements)
  → Academic (categories, courses, specializations, syllabi, subjects)
  → Student (enrollment, roll number, course, documents, verification)
  → Examination (sessions, exam forms, subject marks, result computation)
  → Placement (recruiters, placements)
```

Examination **depends on** Academic (Subject, Course) and Student. Subject must be added to Academic before Examination implementation.

---

## 3. Prerequisite: Academic Domain Amendment (Subject)

Before Examination can exist, the Academic domain must include a **Subject** entity.

**Subject** (new table in Academic domain):

| Field         | Type                  | Constraints        | Notes                                |
|---------------|-----------------------|--------------------|--------------------------------------|
| id            | bigint                | PK, auto-increment |                                      |
| course_id     | bigint                | FK courses, RESTRICT | Which course this subject belongs to |
| name          | string                | required           | e.g. "Data Structures"               |
| code          | string                | required           | e.g. "CS301"                         |
| max_marks     | integer               | required           | Maximum marks for this subject       |
| passing_marks | integer               | required           | Minimum marks to pass                |
| is_active     | boolean               | default true       |                                      |
| sort_order    | integer               | default 0          |                                      |
| timestamps    |                       |                    | created_at, updated_at               |

- UNIQUE(course_id, code) — no duplicate codes within a course.
- New permissions: subject.view, subject.create, subject.update, subject.delete.
- AcademicAdmin gets all subject.* permissions.
- Filament SubjectResource under "Academic" navigation group.
- Course model gets subjects() HasMany relationship.

---

## 4. Entities

### 4.1 ExamSession

An exam period spanning multiple courses/subjects. No FK to Course; subjects within the session determine which courses are covered.

| Attribute    | Type           | Constraints        | Notes                                    |
|-------------|----------------|--------------------|------------------------------------------|
| id          | bigint         | PK, auto-increment |                                          |
| name        | string         | required           | e.g. "End-Semester May 2026"             |
| slug        | string         | required, unique   | URL-friendly                              |
| academic_year | string       | required           | e.g. "2025-2026"                         |
| session_type| string         | required           | regular \| supplementary \| improvement  |
| start_date  | date           | nullable           |                                          |
| end_date    | date           | nullable           |                                          |
| status      | string         | required           | draft \| registration_open \| completed \| published |
| published_at| datetime       | nullable           | Set when status becomes published        |
| timestamps  |                |                    | created_at, updated_at                    |

**Rules:** Status transitions are one-way. When status = published, all related ExamForms and SubjectMarks become read-only (see section 6).

---

### 4.2 ExamSessionSubject

Links subjects to a session. Defines which subjects are examined in this session.

| Attribute      | Type           | Constraints        | Notes                    |
|----------------|----------------|--------------------|--------------------------|
| id             | bigint         | PK, auto-increment |                          |
| exam_session_id| bigint         | FK, required, RESTRICT | Belongs to ExamSession  |
| subject_id     | bigint         | FK, required, RESTRICT | Belongs to Subject (Academic) |
| exam_date      | date           | nullable           |                          |
| exam_time      | time           | nullable           |                          |
| timestamps     |                |                    | created_at, updated_at   |

- UNIQUE(exam_session_id, subject_id).

---

### 4.3 ExamForm

Admin registers a student for an exam session. One form per student per session.

| Attribute      | Type           | Constraints        | Notes                    |
|----------------|----------------|--------------------|--------------------------|
| id             | bigint         | PK, auto-increment |                          |
| exam_session_id| bigint         | FK, required, RESTRICT | Belongs to ExamSession  |
| student_id     | bigint         | FK, required, RESTRICT | Belongs to Student      |
| status         | string         | required           | applied \| approved \| rejected |
| approved_at    | datetime       | nullable           | When status became approved/rejected |
| approved_by    | bigint         | FK users, nullable, RESTRICT | Admin user_id        |
| timestamps     |                |                    | created_at, updated_at   |

- UNIQUE(exam_session_id, student_id).

**Invariants (enforced at application level):**
1. ExamForm can only be created when session status is `registration_open`.
2. student.course_id must have at least one Subject included in the ExamSession (via ExamSessionSubject). Prevents registering a student for a session that has no subjects for their course.

---

### 4.4 SubjectMark

One record per student per subject per session.

| Attribute      | Type           | Constraints        | Notes                    |
|----------------|----------------|--------------------|--------------------------|
| id             | bigint         | PK, auto-increment |                          |
| exam_session_id| bigint         | FK, required, RESTRICT | Belongs to ExamSession  |
| student_id     | bigint         | FK, required, RESTRICT | Belongs to Student      |
| subject_id     | bigint         | FK, required, RESTRICT | Belongs to Subject      |
| marks_obtained | decimal(8,2)   | nullable           | null when absent         |
| is_absent      | boolean        | default false      |                          |
| timestamps     |                |                    | created_at, updated_at   |

- UNIQUE(exam_session_id, student_id, subject_id).

**Invariants (enforced at application level):**
1. marks_obtained must be <= subject.max_marks. If is_absent is true, marks_obtained must be null.
2. student.course_id must equal subject.course_id — a student can only receive marks for subjects in their own course.
3. subject must be linked to the session via ExamSessionSubject — cannot enter marks for a subject not in this session.

---

### 4.5 Result Calculation (No Table — Derived Dynamically)

Results are NOT stored. They are computed on-the-fly from SubjectMark records when viewing or publishing.

**total_marks** = sum of subject.max_marks for subjects in the session AND belonging to the student's course.

**Completeness:** A student result can only be computed if a SubjectMark exists for every session subject that belongs to that student's course. Missing rows = incomplete; do not compute or display a result for that student.

**Grade thresholds** are defined in `config/exam.php` (not a database table). Example:

```php
'grades' => [
    ['name' => 'A+', 'min' => 90, 'max' => 100, 'is_passing' => true],
    ['name' => 'A',  'min' => 80, 'max' => 89.99, 'is_passing' => true],
    ['name' => 'B+', 'min' => 70, 'max' => 79.99, 'is_passing' => true],
    ['name' => 'B',  'min' => 60, 'max' => 69.99, 'is_passing' => true],
    ['name' => 'C',  'min' => 50, 'max' => 59.99, 'is_passing' => true],
    ['name' => 'D',  'min' => 40, 'max' => 49.99, 'is_passing' => true],
    ['name' => 'F',  'min' => 0,  'max' => 39.99, 'is_passing' => false],
],
```

**Pass condition:** Overall percentage maps to a grade with is_passing = true AND, for each subject, subject_mark.marks_obtained ≥ subject.passing_marks (per-subject: use that subject's own marks and passing_marks). If is_absent = true for any subject, that subject's marks = 0 and is_passed = false automatically (regardless of overall percentage).

A Result table can be added later if performance demands caching.

---

## 5. Relationships

```
Course 1 ----< N Subject (Academic)
ExamSession 1 ----< N ExamSessionSubject >---- 1 Subject
ExamSession 1 ----< N ExamForm >---- 1 Student
ExamSession 1 ----< N SubjectMark; Student 1 ----< N SubjectMark; Subject 1 ----< N SubjectMark
```

- **ExamSession** has many **ExamSessionSubject**; each links to one **Subject** (Academic).
- **ExamSession** has many **ExamForm**; each links to one **Student**.
- **ExamSession** has many **SubjectMark**; **Student** has many **SubjectMark**; **Subject** has many **SubjectMark** (per session).

---

## 6. Workflows

### 6.1 ExamSession Status

```
draft → registration_open → completed → published
```

- **draft**: Session created; add/remove subjects.
- **registration_open**: ExamForms can be created and approved; registration closes on transition to completed.
- **completed**: Exams done; marks entry enabled.
- **published**: Results visible publicly; session becomes read-only.

**Read-only rule:** When ExamSession.status = published, all related ExamForms and SubjectMarks become read-only. No creation, update, or deletion is permitted on child records of a published session. Enforced at application level (policies and Filament guards).

Transitions are one-way. Each transition is an explicit admin action in Filament, gated by exam-session.update.

### 6.2 ExamForm Status

```
applied → approved | rejected
```

Admin creates form (status applied), then approves or rejects. Only approved students get marks entered.

### 6.3 Result Viewing / Publishing

1. Session status is completed and marks have been entered.
2. Admin can **preview results** at any time (computed from SubjectMark rows):
   - **Completeness rule:** A student result can only be computed if a SubjectMark exists for every session subject that belongs to that student's course. If any such subject has no SubjectMark row, the result is incomplete — do not show a computed result for that student (prevents partial-entry bugs).
   - For each student with an approved ExamForm and complete marks, gather SubjectMarks for subjects (in the session) matching the student's course.
   - total_marks = sum of subject.max_marks for those subjects.
   - marks_obtained = sum of subject_marks.marks_obtained across all subjects (absent subjects contribute 0 marks).
   - percentage = (marks_obtained / total_marks) * 100.
   - Look up grade from config('exam.grades') where percentage falls in range.
   - **Pass condition:** Overall grade has is_passing = true AND, for each subject, subject_mark.marks_obtained ≥ subject.passing_marks (per-subject comparison; use that subject's own marks and passing_marks).
   - **Absent rule:** If is_absent = true for any subject, that subject's marks count as 0 and is_passed = false automatically (regardless of overall percentage).
3. ExaminationCell **publishes** session (status → published, published_at set).
4. Public results page computes and displays results for published sessions only (same completeness and pass/absent rules).

---

## 7. Design Decisions (Locked)

| Decision        | Choice                                                                 |
|----------------|-------------------------------------------------------------------------|
| Result storage | No Result table; derived dynamically from SubjectMark.                   |
| Grade rules    | Config-based (config/exam.php); no GradeRule table.                    |
| Session status | Four states: draft, registration_open, completed, published (no ongoing).|
| ExamForm creation | Admin-only (Phase 1). Session must be registration_open.             |
| ExamForm–course | student.course_id must have ≥1 Subject in session (ExamSessionSubject).|
| SubjectMark    | student.course_id = subject.course_id; subject in session.             |
| Result completeness | Only compute if SubjectMark exists for every session subject of that student's course; missing = incomplete. |
| After publish  | ExamForms and SubjectMarks read-only.                                   |
| Delete strategy| FK RESTRICT everywhere; no cascade.                                    |
| Public results | Yes; search by roll number or enrollment ID; published sessions only.  |

---

## 8. Index Strategy

| Table                 | Index / Unique key                         | Purpose                                       |
|------------------------|--------------------------------------------|-----------------------------------------------|
| subjects               | UNIQUE(course_id, code)                    | No duplicate codes per course                 |
| subjects               | FK(course_id)                              | Filter by course                              |
| exam_sessions          | UNIQUE(slug)                               | URL-friendly lookup                           |
| exam_sessions          | INDEX(status)                              | Filter by workflow state                      |
| exam_session_subjects  | UNIQUE(exam_session_id, subject_id)        | No duplicates                                 |
| exam_forms             | UNIQUE(exam_session_id, student_id)       | One form per student per session              |
| exam_forms             | INDEX(status)                              | Filter pending/approved                       |
| subject_marks           | UNIQUE(exam_session_id, student_id, subject_id) | One mark per student per subject per session |
| subject_marks           | INDEX(student_id, exam_session_id)         | Public result lookup filters by student first |

---

## 9. Permissions (RBAC)

**Permissions to remove** (phantom entries; no Result entity):

- result.view, result.create, result.update, result.publish

**Examination module** (alongside existing exam-session.*):

- exam-form.view, exam-form.create, exam-form.update, exam-form.delete
- subject-mark.view, subject-mark.create, subject-mark.update

**Academic module** (add):

- subject.view, subject.create, subject.update, subject.delete

**Role assignments:**

- **ExaminationCell**: all exam-session.*, exam-form.*, subject-mark.*, plus student.view, student.verify
- **AcademicAdmin**: add subject.*
- **SuperAdmin**: everything

---

## 10. Admin Surface (Filament)

- **SubjectResource** (Academic group): CRUD for subjects per course; searchable/sortable; active toggle.
- **ExamSessionResource** (Examination group): CRUD with status badge; workflow action buttons (Advance Status); relation managers:
  - **SubjectsRelationManager**: add/remove subjects, set exam dates
  - **ExamFormsRelationManager**: register students, approve/reject actions
  - Marks entry: bulk marks entry page or action per subject
  - **Preview Results** action (on completed sessions; computes results dynamically; modal or page)
  - **Publish** action (sets status to published, published_at)
- When session is published, relation managers and marks entry must deny create/update/delete (read-only).

---

## 11. Public Surface

- GET /results — Search form (roll number or enrollment ID input).
- GET /results/search?query=... — Results display for matching student.
- Only shows results from **published** sessions.
- Displays: student name, course, session name, subject-wise marks, total, percentage, grade, pass/fail.
- Add "Results" link to public header navigation.

---

## 12. Test Strategy

- **Subject model** (Academic): factory, uniqueness (course_id, code), FK RESTRICT.
- **ExamSession workflow**: valid status transitions; reject invalid transitions.
- **ExamForm invariants**: uniqueness; only created when registration_open; student course has ≥1 subject in session; approve/reject workflow.
- **SubjectMark validation**: marks <= max_marks; absent handling; uniqueness; student.course_id = subject.course_id; subject in session.
- **Result computation**: dynamic calculation from SubjectMarks; completeness (all session subjects for that course must have a SubjectMark); config-based grade lookup; pass/fail (overall grade + per-subject: subject_mark.marks_obtained ≥ subject.passing_marks); absent ⇒ is_passed = false.
- **Read-only after publish**: cannot create/update/delete ExamForm or SubjectMark when session is published.
- **RBAC**: ExaminationCell full access; AcademicAdmin subject.* and limited exam; Student role forbidden on exam resources.
- **Public results**: published sessions visible; unpublished return 404 or empty; search by roll number / enrollment ID.
- **FK RESTRICT**: cascade prevention on all entities.

---

## 13. Summary

| Item            | Decision                                                                 |
|-----------------|--------------------------------------------------------------------------|
| Entities        | ExamSession, ExamSessionSubject, ExamForm, SubjectMark; Subject (Academic) |
| Result          | No table; derived from SubjectMark; config/exam.php for grades           |
| Session status  | draft → registration_open → completed → published                        |
| ExamForm        | Admin-only; session registration_open; student course has subject in session |
| SubjectMark     | course match + subject in session; absent ⇒ 0 marks, fail                |
| Result completeness | SubjectMark required for every session subject of student's course; else result not computed |
| After publish   | ExamForms and SubjectMarks read-only                                     |
| Delete strategy | FK RESTRICT everywhere                                                  |
| Permissions     | exam-session.*, exam-form.*, subject-mark.*, subject.*; no result.*       |
| Admin           | Filament ExamSessionResource + relation managers; SubjectResource (Academic) |
| Public          | /results search; published sessions only                                |
| Dependencies    | Academic (Subject, Course), Student                                     |

---

## 14. Implementation

Implement in order: (1) Academic amendment: Subject migration, model, factory, seeder, RBAC, SubjectResource; (2) config/exam.php with grades; (3) Examination migrations (exam_sessions, exam_session_subjects, exam_forms, subject_marks), models, factories; (4) RBAC (remove result.*, add exam-form.*, subject-mark.*; role assignments); (5) policies and invariants (ExamForm, SubjectMark, read-only when published); (6) Filament ExamSessionResource with relation managers and actions; (7) public results controller and views; (8) tests per section 12. Same discipline as Academic and Student domains.
