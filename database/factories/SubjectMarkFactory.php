<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\ExamSession;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectMark;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubjectMark>
 */
class SubjectMarkFactory extends Factory
{
    protected $model = SubjectMark::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $examSession = ExamSession::factory()->create();
        $course = Course::factory()->create();
        $student = Student::factory()->create(['course_id' => $course->id]);
        $subject = Subject::factory()->create(['course_id' => $course->id]);

        return [
            'exam_session_id' => $examSession->id,
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'marks_obtained' => fake()->numberBetween(0, $subject->max_marks),
            'is_absent' => false,
        ];
    }

    public function absent(): static
    {
        return $this->state(fn (array $attributes) => [
            'marks_obtained' => null,
            'is_absent' => true,
        ]);
    }
}
