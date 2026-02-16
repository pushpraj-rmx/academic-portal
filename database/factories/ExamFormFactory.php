<?php

namespace Database\Factories;

use App\Models\ExamForm;
use App\Models\ExamSession;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExamForm>
 */
class ExamFormFactory extends Factory
{
    protected $model = ExamForm::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'exam_session_id' => ExamSession::factory(),
            'student_id' => Student::factory(),
            'status' => 'applied',
            'approved_at' => null,
            'approved_by' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'approved_at' => now(),
        ]);
    }
}
