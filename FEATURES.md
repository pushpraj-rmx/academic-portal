# Academic Portal – Feature List & QA Checklist

## Public site

| Feature | Route / action | QA checklist |
|--------|----------------|--------------|
| Home | `GET /` | [ ] Renders; links/nav work |
| Notices list | `GET /notices` | [ ] List loads; links to single |
| Notice single | `GET /notices/{slug}` | [ ] Correct notice; slug 404 |
| Academic index | `GET /academic` | [ ] Categories/courses visible |
| Category page | `GET /academic/category/{slug}` | [ ] Courses for category; slug 404 |
| Course detail | `GET /academic/courses/{slug}` | [ ] Course info; syllabus link; slug 404 |
| Syllabus download | `GET /academic/syllabus/{id}` | [ ] File downloads or 404 |
| Results index | `GET /results` | [ ] Page loads |
| Results search | `GET /results/search` | [ ] Search works; results/empty state |
| Placements – recruiters | `GET /placements/recruiters` | [ ] Recruiters list |
| Placements – statistics | `GET /placements/statistics` | [ ] Stats display |
| Static page | `GET /page/{slug}` | [ ] Page content; slug 404 |

---

## Authentication (Breeze)

| Feature | Route / action | QA checklist |
|--------|----------------|--------------|
| Register | `GET/POST /register` | [ ] Form; validation; redirect after |
| Login | `GET/POST /login` | [ ] Form; wrong creds; redirect |
| Logout | `POST /logout` | [ ] Session cleared; redirect |
| Forgot password | `GET/POST /forgot-password` | [ ] Email sent; message shown |
| Reset password | `GET/POST /reset-password/{token}` | [ ] Form; valid/invalid token |
| Email verification | Verify link + resend | [ ] Verify works; resend throttled |
| Confirm password | When required | [ ] Prompt; correct/incorrect |
| Profile edit | `GET /profile` | [ ] Auth only; form loads |
| Profile update | `PATCH /profile` | [ ] Updates; validation |
| Profile destroy | `DELETE /profile` | [ ] Account deleted; redirect |
| Dashboard | `GET /dashboard` | [ ] Auth only; view loads |

---

## Admin panel (Filament, `/admin`)

| Resource | Path | QA checklist |
|----------|------|--------------|
| Users | `/admin/users` | [ ] List; create; edit; delete; filters/search |
| Roles | `/admin/roles` | [ ] List; create; edit; delete; permissions |
| Announcements | `/admin/announcements` | [ ] CRUD; slug; publish/dates if any |
| Pages | `/admin/pages` | [ ] CRUD; slug; content |
| Course categories | `/admin/course-categories` | [ ] CRUD; slug; ordering if any |
| Courses | `/admin/courses` | [ ] CRUD; category/specialization; slug |
| Specializations | `/admin/specializations` | [ ] CRUD |
| Syllabi | `/admin/syllabi` | [ ] CRUD; course link; file upload |
| Subjects | `/admin/subjects` | [ ] CRUD |
| Exam sessions | `/admin/exam-sessions` | [ ] CRUD; relation managers (subjects, forms, marks) |
| Students | `/admin/students` | [ ] CRUD; documents relation manager |
| Recruiters | `/admin/recruiters` | [ ] CRUD |
| Placements | `/admin/placements` | [ ] CRUD; recruiter link |

**Admin-only route**

| Feature | Route | QA checklist |
|--------|--------|--------------|
| Student document download | `GET /admin/student-documents/{id}/download` (auth) | [ ] File downloads; 403/404 when invalid |

---

## Models (reference)

User, Role (Spatie), Announcement, Page, CourseCategory, Course, Specialization, Syllabus, Subject, ExamSession, ExamSessionSubject, ExamForm, SubjectMark, Student, StudentDocument, Recruiter, Placement.

---

## Quick smoke test

1. [ ] Home loads; one notice and one academic link work.
2. [ ] Results and placements (recruiters + statistics) load.
3. [ ] Register → verify (or skip) → login → profile edit → logout.
4. [ ] Admin login → open each resource (list/create one item) → one student document download.
