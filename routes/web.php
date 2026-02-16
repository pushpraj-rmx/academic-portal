<?php

use App\Http\Controllers\AcademicController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResultController;
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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/admin/student-documents/{studentDocument}/download', [\App\Http\Controllers\StudentDocumentController::class, 'download'])
        ->name('admin.student-documents.download');
});

require __DIR__.'/auth.php';

Route::get('/page/{page:slug}', [PageController::class, 'show'])->name('pages.show');
