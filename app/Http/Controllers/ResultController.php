<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Services\ExamResultService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResultController extends Controller
{
    public function index(): View
    {
        return view('public.results.index');
    }

    public function search(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'query' => ['required', 'string', 'max:255'],
        ]);

        $query = trim($request->input('query'));

        $student = Student::with('user', 'course')
            ->where('roll_number', $query)
            ->orWhere('enrollment_id', $query)
            ->first();

        if (! $student) {
            return view('public.results.show', [
                'student' => null,
                'resultsBySession' => [],
            ]);
        }

        $examSessionIds = $student->examForms()
            ->approved()
            ->whereHas('examSession', fn ($q) => $q->where('status', 'published'))
            ->pluck('exam_session_id');

        $service = app(ExamResultService::class);
        $resultsBySession = [];

        foreach ($examSessionIds as $sessionId) {
            $session = \App\Models\ExamSession::find($sessionId);
            if (! $session) {
                continue;
            }

            $allResults = $service->computeResultsForSession($session);
            $studentResult = collect($allResults)->firstWhere('student_id', $student->id);

            if ($studentResult !== null) {
                $resultsBySession[] = [
                    'session' => $session,
                    'result' => $studentResult,
                ];
            }
        }

        return view('public.results.show', [
            'student' => $student,
            'resultsBySession' => $resultsBySession,
        ]);
    }
}
