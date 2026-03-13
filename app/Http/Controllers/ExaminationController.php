<?php

namespace App\Http\Controllers;

use App\Models\DownloadableForm;
use App\Models\Faq;
use App\Models\GradingRule;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExaminationController extends Controller
{
    public function center(): View
    {
        return view('public.examination.center');
    }

    public function notes(): View
    {
        return view('public.examination.notes');
    }

    public function faqs(): View
    {
        $faqs = Faq::query()
            ->published()
            ->where('category', 'examination')
            ->ordered()
            ->get();

        return view('public.examination.faqs', [
            'faqs' => $faqs,
        ]);
    }

    public function gradingSystem(): View
    {
        $gradingRules = GradingRule::query()
            ->ordered()
            ->get();

        return view('public.examination.grading-system', [
            'gradingRules' => $gradingRules,
        ]);
    }

    public function forms(): View
    {
        $forms = DownloadableForm::query()
            ->published()
            ->where('category', 'exam')
            ->ordered()
            ->get();

        return view('public.examination.forms', [
            'forms' => $forms,
        ]);
    }

    public function downloadForm(DownloadableForm $downloadableForm): BinaryFileResponse
    {
        if (! $downloadableForm->is_published) {
            abort(404);
        }

        return response()->download(
            Storage::disk('public')->path($downloadableForm->file_path),
            basename($downloadableForm->file_path)
        );
    }
}
