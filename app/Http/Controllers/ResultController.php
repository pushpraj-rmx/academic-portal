<?php

namespace App\Http\Controllers;

use App\Models\ExamSession;
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
        $requestedExamSessionId = $request->filled('exam_session_id')
            ? (int) $request->input('exam_session_id')
            : null;

        $student = Student::with('user', 'course')
            ->where('roll_number', $query)
            ->orWhere('enrollment_id', $query)
            ->first();

        if (! $student) {
            return view('public.results.show', [
                'student' => null,
                'availableSessions' => [],
                'selectedSessionId' => null,
                'query' => $query,
                'resultsBySession' => [],
            ]);
        }

        $examSessionIds = $student->examForms()
            ->approved()
            ->whereHas('examSession', fn ($q) => $q->where('status', 'published'))
            ->pluck('exam_session_id');

        $availableSessions = ExamSession::query()
            ->whereIn('id', $examSessionIds)
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->get();

        $service = app(ExamResultService::class);
        $studentResultsBySessionId = [];

        foreach ($availableSessions as $session) {
            if (! $session instanceof ExamSession) {
                continue;
            }

            $allResults = $service->computeResultsForSession($session);
            $studentResult = collect($allResults)->firstWhere('student_id', $student->id);

            if ($studentResult !== null) {
                $studentResultsBySessionId[$session->id] = [
                    'session' => $session,
                    'result' => $studentResult,
                ];
            }
        }

        $selectedSessionId = null;
        if ($requestedExamSessionId !== null && isset($studentResultsBySessionId[$requestedExamSessionId])) {
            $selectedSessionId = $requestedExamSessionId;
        } else {
            // Default to the latest session that actually has a computed result for this student.
            $latestResultSession = collect($availableSessions)
                ->first(fn (ExamSession $s): bool => isset($studentResultsBySessionId[$s->id]));

            $selectedSessionId = $latestResultSession?->id ?? $availableSessions->first()?->id;
        }

        $resultsBySession = $selectedSessionId !== null && isset($studentResultsBySessionId[$selectedSessionId])
            ? [$studentResultsBySessionId[$selectedSessionId]]
            : [];

        return view('public.results.show', [
            'student' => $student,
            'availableSessions' => $availableSessions,
            'selectedSessionId' => $selectedSessionId,
            'query' => $query,
            'resultsBySession' => $resultsBySession,
        ]);
    }
}
