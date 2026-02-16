<?php

namespace Database\Factories;

use App\Models\ExamSession;
use App\Models\ExamSessionSubject;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExamSessionSubject>
 */
class ExamSessionSubjectFactory extends Factory
{
    protected $model = ExamSessionSubject::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $examSession = ExamSession::factory()->create();
        $subject = Subject::factory()->create();

        return [
            'exam_session_id' => $examSession->id,
            'subject_id' => $subject->id,
            'exam_date' => fake()->optional()->dateTimeBetween('-1 week', '+1 week'),
            'exam_time' => null,
        ];
    }
}
