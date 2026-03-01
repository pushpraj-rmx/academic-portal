# Academic Portal – Admin System Reference

This document describes how the system works for administrators. Use it as a reference when managing content and data; you can update it as the app evolves and later turn it into in-app help.

---

## 1. Overview

- **Admin panel:** Filament at `/admin`. Only users with a role (other than *Student* only) can access it.
- **Roles (Spatie):** `super-admin`, `academic-admin`, `examination-cell`, `placement-cell`, `content-manager`, `student`. Students cannot access the admin panel.
- **Public site:** Home, notices, academic (categories/courses), results search, placements, and static pages. Content is driven by what you create and publish in the admin panel.

---

## 2. How the Public Site Gets Its Content

| Public area | Source in admin | Notes |
|-------------|------------------|--------|
| **Home** | **Pages** – page with slug `home` must exist and be published. Optional cards from pages with slugs `about`, `notices`, `director-message`. | Unpublishing or deleting the `home` page will 404 the home route. |
| **Notices** | **Announcements** | Only `is_published` announcements appear. Listed by slug on `/notices`, single at `/notices/{slug}`. |
| **Academic** | **Course categories** → **Courses** → **Specializations**, **Syllabi**, **Subjects** | Categories and courses must be `is_active`. Syllabi shown per course (one active per course/specialization). |
| **Results** | **Exam sessions**, **Exam session subjects**, **Exam forms**, **Subject marks**, **Students**, **Subjects** | Student searches by roll number or enrollment ID. Results shown only for approved exam forms in published exam sessions. |
| **Placements** | **Recruiters**, **Placements** | Recruiters list and statistics pages use these. |
| **Static pages** | **Pages** | Any other published page is available at `/{slug}`. Slug must be unique. |

---

## 3. Resources (What They Are and How They Relate)

### 3.1 Users and access

- **Users** – Login accounts (Breeze). Can have multiple **roles** (Spatie). Users with only the *Student* role cannot open the admin panel.
- **Roles** – Define permissions. Assign roles to users to control what they can do in the admin.

### 3.2 Content (site-facing)

- **Announcements** – Notices on the public site. Fields: title, slug, body, optional attachment, `is_published`, `published_at`. Slug is used in the URL (`/notices/{slug}`).
- **Pages** – Static content. Fields: title, slug, body, meta_description, `is_published`, sort_order. Special slugs: `home` (main homepage), and optionally `about`, `notices`, `director-message` for home cards. Any other slug is available at `/{slug}`.

### 3.3 Academic structure

- **Course categories** – Top-level grouping (e.g. “Undergraduate”, “Postgraduate”). Have slug, description, `is_active`, sort_order. Order: sort_order then name.
- **Courses** – Belong to one **course category**. Have name, slug, duration, intake, eligibility, description, `is_active`, sort_order. A course has many **specializations**, **syllabi**, and **subjects**.
- **Specializations** – Belong to one **course**. Optional subdivision of a course (name, slug, description, etc.). Used e.g. for syllabus variants.
- **Syllabi** – Belong to one **course**, optionally one **specialization**. Store `academic_year`, version, file (file_path), `is_active`, `published_at`. Only one syllabus per course (or per course+specialization) should be active; activating one deactivates others for that course/specialization. Public syllabus download is at `/academic/syllabus/{id}`.
- **Subjects** – Belong to one **course**. Have name, code, max_marks, passing_marks, `is_active`, sort_order. Used in exam sessions and for marks.

Relationship chain: **Course category** → **Courses** → **Specializations** | **Syllabi** | **Subjects**.

### 3.4 Examinations and results

- **Exam sessions** – An exam period (name, slug, academic_year, session_type, start_date, end_date, status, published_at). Has many **exam session subjects**, **exam forms**, and **subject marks**. Public results only show data for sessions with `status = published`.
- **Exam session subjects** – Pivot/schedule: which **subject** is in this **exam session**, plus exam_date, exam_time. Links **Exam session** to **Subject** (subjects belong to courses).
- **Exam forms** – A student’s form for an exam session (exam_session_id, student_id, status, approved_at, approved_by). Only **approved** forms are used for public results. Student must exist and be linked to the same course as the session’s subjects (conceptually).
- **Subject marks** – Marks per student per subject per exam session (exam_session_id, student_id, subject_id, marks_obtained, is_absent). Used by the result service to compute and display results.

Result flow: **Student** (enrollment_id / roll_number) → **Exam forms** (approved) → **Exam session** (published) → **Subject marks** → shown on public **Results** search.

### 3.5 Students

- **Students** – Link a **user** to institutional data: user_id, course_id, enrollment_id, roll_number, phone, alternate_phone, verification_status, verified_at, verified_by, notes. A student has many **documents**, **exam forms**, **subject marks**, and **placements**.
- **Student documents** – Files attached to a student (student_id, type, file_path, original_name, verified, verified_at). Admins can download via `/admin/student-documents/{id}/download` (auth required).

### 3.6 Placements

- **Recruiters** – Companies (name, slug, website, logo_path, contact details, is_active, sort_order). Shown on public placements/recruiters.
- **Placements** – A placement record links a **student** to a **recruiter** (academic_year, placement_type, designation, package_amount, status, offer_date). Used for placements statistics and reporting.

---

## 4. Key Workflows (Admin Side)

### 4.1 Home and static content

1. Ensure a **Page** with slug `home` exists and is published so the site home loads.
2. Optionally create and publish pages with slugs `about`, `notices`, `director-message` for home cards (order fixed by slug order in code).
3. Create other **Pages** for static content; use unique slugs. Publish to make them visible at `/{slug}`.

### 4.2 Notices

1. Create **Announcements** (title, slug, body, optional attachment).
2. Set `is_published` and optionally `published_at` so they appear on `/notices`.

### 4.3 Academic (categories, courses, syllabi)

1. Create **Course categories** (active, sort_order). Create **Courses** under each category (active, sort_order).
2. Optionally add **Specializations** to courses.
3. Add **Subjects** to courses (for exams and marks).
4. For each course (and optional specialization), upload **Syllabi** and set one as active so the public course page shows the right syllabus and download link.

### 4.4 Results (exam sessions and marks)

1. Create **Exam sessions** and set status to `published` when they should be visible in results.
2. Attach **Exam session subjects** (subjects + exam date/time) to the session. Subjects must belong to courses.
3. For each student taking the exam, create an **Exam form** for that session and set status to **approved**.
4. Enter **Subject marks** for each student/subject/session. The public results search uses approved forms and published sessions and computes results from these marks.

### 4.5 Students and documents

1. Create/link **Users** and create **Students** (user, course, enrollment_id, roll_number, etc.). Verification status is for your workflow.
2. Attach **Student documents** via the student’s relation manager. Download from admin: Student → documents → download (uses `/admin/student-documents/{id}/download`).

### 4.6 Placements

1. Create **Recruiters** (active so they show on the recruiters page).
2. Create **Placements** linking **Student** and **Recruiter** with academic year, designation, package, status, etc. Statistics and lists on the public site are built from this data.

---

## 5. Admin-Only Routes

- **Student document download:** `GET /admin/student-documents/{id}/download` – authenticated admin only; returns the file or 403/404.

---

## 6. Quick Reference – Resource Locations in Admin

| Resource | Admin path |
|----------|------------|
| Users | `/admin/users` |
| Roles | `/admin/roles` |
| Announcements | `/admin/announcements` |
| Pages | `/admin/pages` |
| Course categories | `/admin/course-categories` |
| Courses | `/admin/courses` |
| Specializations | `/admin/specializations` |
| Syllabi | `/admin/syllabi` |
| Subjects | `/admin/subjects` |
| Exam sessions | `/admin/exam-sessions` |
| Students | `/admin/students` |
| Recruiters | `/admin/recruiters` |
| Placements | `/admin/placements` |

---

*Update this document as the application changes. When the app is complete, this content can be used to build in-app help for admins.*
