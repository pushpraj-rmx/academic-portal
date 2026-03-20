## 20-Minute Demo Script (Includes CSV Imports) — Markdown

### Segment 1 (0:00–1:30) — What this system does
- On screen: Public homepage → `/results` quick mention → Admin sidebar groups
- Do: Open a quick “flow view” (Student → Exam Session → Subject Marks → Results/Placement → Public visibility)
- Say:
  - “This is an admin-driven, Excel-mode workflow. Admins upload students and marks in bulk, while public results are computed automatically from the marks.”

---

### Segment 2 (1:30–3:30) — Admin login + navigation groups
- On screen: Admin login → Admin sidebar
- Do: Show groups briefly: `Student`, `Examination`, `Data Import`, `Academic`, `Placement`, `Content`, `Settings`
- Say:
  - “We’ll focus on the bulk import pages: Import students and Import subject marks.”

---

### Segment 3 (3:30–5:00) — Academic setup (so imports validate)
- On screen: `Academic → Course Categories → Courses → Subjects`
- Do:
  - Open at least one `Subject` and confirm `code`, `max_marks`, `passing_marks`
- Say:
  - “Subject marks import requires subject codes that exist and are attached to the selected exam session.”

---

### Segment 4 (5:00–7:30) — Create Exam Session + attach subjects
- On screen: `Examination → Exam sessions`
- Do:
  - Open/create an `Exam session` (use a draft/registration state if possible)
  - Go to `Exam Session Subjects` relation manager
  - Ensure `MATH101` (example) is attached to this session
- Say:
  - “This is the gate for marks import. If a subject code isn’t attached to the chosen exam session, rows are rejected.”

---

### Segment 5 (7:30–11:00) — CSV Import #1: Students (Upload → Validate → Import)
- On screen: `Data Import → Import students`
- Do:
  - Click `Download sample CSV`
  - Save it locally and ensure the header matches the sample
  - Upload via `Student Excel file` (FileUpload supports `csv` and Excel types)
  - (Optional) Choose `Default course (optional)` if your CSV uses course by slug/name
  - Confirm `Dry run` is ON (it should default to validate-only)
  - Click `Validate file`
  - Review the preview table + “Validation complete: X valid, Y invalid”
  - Turn `Dry run` OFF
  - Click `Import {validCount} valid rows`
- Say:
  - “This import is two-phase. First we validate and preview rows. Then we commit only the valid rows.”
  - “By default, the imported students get a default password of `password` (they can reset it later).”

---

### Segment 6 (11:00–12:15) — Verify students created (quick proof)
- On screen: `Student → Students` table
- Do:
  - Confirm new student rows appear
  - Confirm `enrollment_id` was generated automatically (if you left it blank in CSV)
- Say:
  - “Now students exist in the system and are ready for exam forms and marks.”

---

### Segment 7 (12:15–14:00) — Exam Forms: Approve the gate for marks import
- On screen: still inside `Exam sessions` → open the same session
- Do:
  - Open the `Exam Forms` relation manager
  - Create an exam form for the imported student (if needed)
  - Click `Approve` for forms with status `applied`
- Say:
  - “Marks import requires an approved exam form for the student in this exam session. That’s why we approve the form before importing marks.”

---

### Segment 8 (14:00–16:45) — CSV Import #2: Subject Marks (Dry run → Import)
- On screen: `Examination → Import subject marks`
- Do:
  - Select the same `Exam session`
  - Click `Download sample CSV` (subject marks sample)
  - Paste rows into `Paste tab- or comma-separated data` with columns:
    - `enrollment_id    subject_code    marks    absent`
  - Set `Dry run` = ON
  - Click `Validate only (dry run)`
  - Check the “Import summary” and any listed errors
  - Set `Dry run` = OFF
  - Click `Import marks`
- Say:
  - “Again, two-phase behavior: validate first, then commit.”
  - “If you see errors like ‘subject code is not part of this exam session’, it means that code wasn’t attached in `Exam Session Subjects`.”

---

### Segment 9 (16:45–18:30) — Publish session + preview results (computed from marks)
- On screen: `Exam sessions` → open session
- Do:
  - Set session status to `published` (if your workflow uses `completed` → `published`)
  - Click `Preview Results`
- Say:
  - “Results are computed dynamically from `SubjectMark`. Once the session is published, students can see results on the public site.”

---

### Segment 10 (18:30–19:30) — Public Results: search and confirm
- On screen: Public page `/results`
- Do:
  - Search by roll number or enrollment id
  - Confirm totals/percentage/grade match the marks we imported
- Say:
  - “Public results come from the computed marks—no separate manual result entry table.”

---

### Segment 11 (19:30–20:00) — Placement quick wrap-up (optional but good ending)
- On screen: `Placement` resources/pages
- Do:
  - Create/open recruiter
  - Add placement for one student
- Say:
  - “Finally, placement is handled independently, but still tied to the student records created by the admin.”

---

## Optional “Show These Error/Guardrails” (30–45 seconds)
Use this if you want one mini moment of “why validation matters”:
- Paste one subject row with a subject code that is NOT in the session → show the exact skip error
- Then paste the correct subject code → show import success

---

If you want, tell me which subject code you’re using in your demo (e.g. `MATH101`) and one example enrollment id format you prefer to show, and I’ll adapt the narration so the values on screen match perfectly.