<?php

namespace App\Http\Controllers;

use App\Models\Placement;
use App\Models\Recruiter;
use Illuminate\Http\Response;

class PlacementController extends Controller
{
    public function recruiters(): Response
    {
        $recruiters = Recruiter::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->view('public.placements.recruiters', ['recruiters' => $recruiters]);
    }

    public function statistics(): Response
    {
        $byYear = Placement::query()
            ->selectRaw('academic_year, count(*) as total')
            ->groupBy('academic_year')
            ->orderByDesc('academic_year')
            ->get();

        $byCourse = Placement::query()
            ->join('students', 'placements.student_id', '=', 'students.id')
            ->join('courses', 'students.course_id', '=', 'courses.id')
            ->selectRaw('courses.id, courses.name as course_name, count(*) as total')
            ->groupBy('courses.id', 'courses.name')
            ->orderByDesc('total')
            ->get();

        $highestPackage = Placement::query()
            ->whereNotNull('package_amount')
            ->max('package_amount');

        return response()->view('public.placements.statistics', [
            'byYear' => $byYear,
            'byCourse' => $byCourse,
            'highestPackage' => $highestPackage,
        ]);
    }
}
