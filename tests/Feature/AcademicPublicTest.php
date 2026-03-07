<?php

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Syllabus;
use Illuminate\Support\Facades\Storage;

test('academic index lists only active categories', function () {
    $active = CourseCategory::factory()->create(['name' => 'Active Cat', 'is_active' => true]);
    CourseCategory::factory()->inactive()->create(['name' => 'Inactive Cat', 'slug' => 'inactive-cat']);

    $response = $this->get(route('academic.index'));

    $response->assertSuccessful();
    $response->assertViewIs('public.academic.index');
    $response->assertSee('Active Cat', false);
    $response->assertDontSee('Inactive Cat', false);
});

test('academic index shows category image when set', function () {
    $category = CourseCategory::factory()->create([
        'is_active' => true,
        'name' => 'Engineering',
        'slug' => 'engineering',
        'image_path' => 'course-categories/engineering.jpg',
        'image_alt' => 'Engineering courses',
    ]);

    $response = $this->get(route('academic.index'));

    $response->assertSuccessful();
    $response->assertSee('Engineering', false);
    $response->assertSee('storage/course-categories/engineering.jpg', false);
    $response->assertSee('Engineering courses', false);
});

test('category page shows only active courses when category is active', function () {
    $category = CourseCategory::factory()->create(['is_active' => true]);
    $activeCourse = Course::factory()->create(['course_category_id' => $category->id, 'name' => 'Active Course', 'is_active' => true]);
    Course::factory()->inactive()->create(['course_category_id' => $category->id, 'name' => 'Inactive Course', 'slug' => 'inactive-course']);

    $response = $this->get(route('academic.category.show', $category));

    $response->assertSuccessful();
    $response->assertSee('Active Course', false);
    $response->assertDontSee('Inactive Course', false);
});

test('inactive category returns 404 for category page', function () {
    $category = CourseCategory::factory()->inactive()->create(['slug' => 'inactive-cat']);

    $response = $this->get(route('academic.category.show', $category));

    $response->assertNotFound();
});

test('course detail returns 404 when category is inactive', function () {
    $category = CourseCategory::factory()->inactive()->create(['slug' => 'inactive-cat']);
    $course = Course::factory()->create(['course_category_id' => $category->id, 'slug' => 'some-course']);

    $response = $this->get(route('academic.course.show', $course));

    $response->assertNotFound();
});

test('course detail returns 404 when course is inactive', function () {
    $category = CourseCategory::factory()->create(['slug' => 'active-cat']);
    $course = Course::factory()->inactive()->create(['course_category_id' => $category->id, 'slug' => 'inactive-course']);

    $response = $this->get(route('academic.course.show', $course));

    $response->assertNotFound();
});

test('course detail shows when category and course are active', function () {
    $category = CourseCategory::factory()->create(['name' => 'Cat', 'slug' => 'cat']);
    $course = Course::factory()->create(['course_category_id' => $category->id, 'name' => 'My Course', 'slug' => 'my-course']);

    $response = $this->get(route('academic.course.show', $course));

    $response->assertSuccessful();
    $response->assertSee('My Course', false);
});

test('syllabus download returns 200 for active syllabus under active course', function () {
    Storage::fake('public');
    Storage::disk('public')->put('syllabus/test.pdf', '%PDF-1.4 fake content');

    $category = CourseCategory::factory()->create(['is_active' => true]);
    $course = Course::factory()->create(['course_category_id' => $category->id, 'is_active' => true]);
    $syllabus = Syllabus::factory()->create([
        'course_id' => $course->id,
        'file_path' => 'syllabus/test.pdf',
        'is_active' => true,
    ]);

    $response = $this->get(route('academic.syllabus.download', $syllabus));

    $response->assertSuccessful();
    $response->assertHeader('Content-Type', 'application/pdf');
});

test('syllabus download returns 404 for inactive syllabus', function () {
    Storage::fake('public');
    Storage::disk('public')->put('syllabus/inactive.pdf', '%PDF fake');

    $category = CourseCategory::factory()->create(['is_active' => true]);
    $course = Course::factory()->create(['course_category_id' => $category->id, 'is_active' => true]);
    $syllabus = Syllabus::factory()->inactive()->create([
        'course_id' => $course->id,
        'file_path' => 'syllabus/inactive.pdf',
    ]);

    $response = $this->get(route('academic.syllabus.download', $syllabus));

    $response->assertNotFound();
});

test('syllabus download returns 404 when course is inactive', function () {
    Storage::fake('public');
    Storage::disk('public')->put('syllabus/off.pdf', '%PDF fake');

    $category = CourseCategory::factory()->create(['is_active' => true]);
    $course = Course::factory()->inactive()->create(['course_category_id' => $category->id]);
    $syllabus = Syllabus::factory()->create([
        'course_id' => $course->id,
        'file_path' => 'syllabus/off.pdf',
        'is_active' => true,
    ]);

    $response = $this->get(route('academic.syllabus.download', $syllabus));

    $response->assertNotFound();
});

test('category list order is deterministic by sort_order then name', function () {
    CourseCategory::factory()->create(['name' => 'Beta', 'slug' => 'beta', 'sort_order' => 1]);
    CourseCategory::factory()->create(['name' => 'Alpha', 'slug' => 'alpha', 'sort_order' => 1]);
    CourseCategory::factory()->create(['name' => 'First', 'slug' => 'first', 'sort_order' => 0]);

    $response = $this->get(route('academic.index'));

    $response->assertSuccessful();
    $html = $response->getContent();
    $posFirst = strpos($html, 'First');
    $posAlpha = strpos($html, 'Alpha');
    $posBeta = strpos($html, 'Beta');
    expect($posFirst)->toBeLessThan($posAlpha);
    expect($posAlpha)->toBeLessThan($posBeta);
});
