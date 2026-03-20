<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentVerificationSearchRequest;
use App\Models\DownloadableForm;
use App\Models\Page;
use App\Models\Student;
use Illuminate\View\View;

class StudentVerificationController extends Controller
{
    public function index(): View
    {
        return view('public.students.verification', [
            'student' => null,
            'query' => null,
        ]);
    }

    public function search(StudentVerificationSearchRequest $request): View
    {
        $query = trim($request->validated('query'));

        $student = Student::query()
            ->with('user', 'course')
            ->where(function ($q) use ($query) {
                $q->where('roll_number', $query)->orWhere('enrollment_id', $query);
            })
            ->first();

        return view('public.students.verification', [
            'student' => $student,
            'query' => $query,
        ]);
    }

    public function applicationForms(): View
    {
        $forms = DownloadableForm::query()
            ->published()
            ->where('category', 'admission')
            ->ordered()
            ->get();

        return view('public.students.application-forms', [
            'forms' => $forms,
        ]);
    }

    public function payFee(): View
    {
        $page = Page::query()
            ->published()
            ->where('slug', 'pay-fee')
            ->firstOrFail();

        return view('public.students.pay-fee', [
            'page' => $page,
        ]);
    }
}
