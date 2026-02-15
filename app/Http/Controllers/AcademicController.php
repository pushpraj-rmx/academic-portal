<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Syllabus;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class AcademicController extends Controller
{
    public function index(): Response
    {
        $categories = CourseCategory::query()
            ->withoutGlobalScopes()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->withCount(['courses' => fn ($q) => $q->where('is_active', true)])
            ->get();

        return response()->view('public.academic.index', ['categories' => $categories]);
    }

    public function categoryShow(CourseCategory $courseCategory): Response
    {
        if (! $courseCategory->is_active) {
            abort(404);
        }

        $courses = $courseCategory->courses()
            ->withoutGlobalScopes()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->view('public.academic.category', [
            'category' => $courseCategory,
            'courses' => $courses,
        ]);
    }

    public function courseShow(Course $course): Response
    {
        $course->load(['courseCategory', 'specializations' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')->orderBy('name'), 'syllabi' => fn ($q) => $q->where('is_active', true)]);

        if (! $course->courseCategory->is_active || ! $course->is_active) {
            abort(404);
        }

        return response()->view('public.academic.course', ['course' => $course]);
    }

    public function syllabusDownload(Syllabus $syllabus): SymfonyResponse
    {
        $syllabus->load('course.courseCategory');

        if (! $syllabus->is_active || ! $syllabus->course->is_active || ! $syllabus->course->courseCategory->is_active) {
            abort(404);
        }

        $path = Storage::disk('public')->path($syllabus->file_path);

        if (! file_exists($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.basename($syllabus->file_path).'"',
        ]);
    }
}
