<?php

use App\Enums\UserRole;
use App\Http\Controllers\AcademicController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ExaminationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PlacementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\StudentVerificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');

Route::get('/notices', [AnnouncementController::class, 'index'])->name('notices.index');
Route::get('/notices/{announcement:slug}', [AnnouncementController::class, 'show'])->name('notices.show');

Route::get('/academic', [AcademicController::class, 'index'])->name('academic.index');
Route::get('/academic/category/{course_category:slug}', [AcademicController::class, 'categoryShow'])->name('academic.category.show');
Route::get('/academic/courses/{course:slug}', [AcademicController::class, 'courseShow'])->name('academic.course.show');
Route::get('/academic/syllabus/{syllabus}', [AcademicController::class, 'syllabusDownload'])->name('academic.syllabus.download');

Route::get('/results', [ResultController::class, 'index'])->name('results.index');
Route::get('/results/search', [ResultController::class, 'search'])->name('results.search');

Route::get('/placements/recruiters', [PlacementController::class, 'recruiters'])->name('placements.recruiters');
Route::get('/placements/statistics', [PlacementController::class, 'statistics'])->name('placements.statistics');
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/examination/forms', [ExaminationController::class, 'forms'])->name('examination.forms');
Route::get('/examination/center', [ExaminationController::class, 'center'])->name('examination.center');
Route::get('/examination/faqs', [ExaminationController::class, 'faqs'])->name('examination.faqs');
Route::get('/examination/grading-system', [ExaminationController::class, 'gradingSystem'])->name('examination.grading-system');
Route::get('/downloads/{downloadableForm}', [ExaminationController::class, 'downloadForm'])->name('downloads.form');

Route::get('/students/verification', [StudentVerificationController::class, 'index'])->name('students.verification');
Route::get('/students/verification/search', [StudentVerificationController::class, 'search'])->name('students.verification.search');
Route::get('/students/application-forms', [StudentVerificationController::class, 'applicationForms'])->name('students.application-forms');
Route::get('/students/pay-fee', [StudentVerificationController::class, 'payFee'])->name('students.pay-fee');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/student', function () {
        if (! auth()->user()->hasRole(UserRole::Student->value)) {
            return redirect()->route('filament.admin.pages.dashboard');
        }

        return view('student.dashboard');
    })->name('student.dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/admin/student-documents/{studentDocument}/download', [\App\Http\Controllers\StudentDocumentController::class, 'download'])
        ->name('admin.student-documents.download');
});

require __DIR__.'/auth.php';

// Catch-all for CMS pages only; allow only known page slugs so /admin and /admin/* stay with Filament
Route::get('/{page:slug}', [PageController::class, 'show'])
    ->name('pages.show')
    ->where('page', '^(about|director-message|vision-mission|notices|pay-fee)$');
