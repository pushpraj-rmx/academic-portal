<?php

namespace App\Services;

use App\Models\ExamSession;
use App\Models\Student;

class ExamResultService
{
    /**
     * Compute results for all students with approved ExamForm in the session.
     * Returns only complete results (every session subject for that student's course has a SubjectMark).
     *
     * @return array<int, array{student_id: int, student: Student, total_marks: float, marks_obtained: float, percentage: float, grade: string, is_passed: bool, subject_marks_detail: array}>
     */
    public function computeResultsForSession(ExamSession $session): array
    {
        $grades = config('exam.grades', []);
        $approvedFormStudentIds = $session->examForms()->approved()->pluck('student_id')->unique();

        $sessionSubjectIdsByCourse = $session->examSessionSubjects()
            ->with('subject:id,course_id,max_marks,passing_marks')
            ->get()
            ->groupBy(fn ($ess) => $ess->subject->course_id)
            ->map(fn ($group) => $group->pluck('subject_id')->values()->all());

        $results = [];
        foreach ($approvedFormStudentIds as $studentId) {
            $student = Student::with('course')->find($studentId);
            if (! $student) {
                continue;
            }

            $courseId = $student->course_id;
            $sessionSubjectIds = $sessionSubjectIdsByCourse->get($courseId, []);
            if ($sessionSubjectIds === []) {
                continue;
            }

            $subjectMarks = $session->subjectMarks()
                ->where('student_id', $studentId)
                ->whereIn('subject_id', $sessionSubjectIds)
                ->with('subject:id,course_id,max_marks,passing_marks')
                ->get()
                ->keyBy('subject_id');

            if ($subjectMarks->count() < count($sessionSubjectIds)) {
                continue;
            }

            $totalMarks = 0.0;
            $marksObtained = 0.0;
            $allPassedPerSubject = true;
            $anyAbsent = false;

            foreach ($sessionSubjectIds as $subjectId) {
                $sm = $subjectMarks->get($subjectId);
                if (! $sm) {
                    $allPassedPerSubject = false;
                    break;
                }
                $subj = $sm->subject;
                $totalMarks += (float) $subj->max_marks;
                $obtained = $sm->is_absent ? 0 : (float) ($sm->marks_obtained ?? 0);
                $marksObtained += $obtained;
                if ($sm->is_absent) {
                    $anyAbsent = true;
                }
                if ($obtained < (float) $subj->passing_marks) {
                    $allPassedPerSubject = false;
                }
            }

            $percentage = $totalMarks > 0 ? ($marksObtained / $totalMarks) * 100 : 0.0;
            $grade = $this->findGrade($percentage, $grades);
            $isPassed = ! $anyAbsent && $allPassedPerSubject && ($grade['is_passing'] ?? false);

            $results[] = [
                'student_id' => $studentId,
                'student' => $student,
                'total_marks' => $totalMarks,
                'marks_obtained' => $marksObtained,
                'percentage' => round($percentage, 2),
                'grade' => $grade['name'] ?? 'F',
                'is_passed' => $isPassed,
                'subject_marks_detail' => $subjectMarks->values()->all(),
            ];
        }

        return $results;
    }

    /**
     * @param  array<int, array{name: string, min: float, max: float, is_passing: bool}>  $grades
     * @return array{name: string, is_passing: bool}
     */
    private function findGrade(float $percentage, array $grades): array
    {
        foreach ($grades as $g) {
            $min = (float) ($g['min'] ?? 0);
            $max = (float) ($g['max'] ?? 0);
            if ($percentage >= $min && $percentage <= $max) {
                return [
                    'name' => $g['name'] ?? 'F',
                    'is_passing' => (bool) ($g['is_passing'] ?? false),
                ];
            }
        }

        return ['name' => 'F', 'is_passing' => false];
    }
}
