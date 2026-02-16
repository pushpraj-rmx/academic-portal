<?php

namespace Database\Factories;

use App\Models\ExamSession;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExamSession>
 */
class ExamSessionFactory extends Factory
{
    protected $model = ExamSession::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = 'Exam '.fake()->monthName().' '.fake()->year();

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 9999),
            'academic_year' => (string) (fake()->year() - 1).'-'.fake()->year(),
            'session_type' => 'regular',
            'start_date' => fake()->dateTimeBetween('-1 month', '+1 month'),
            'end_date' => fake()->dateTimeBetween('+1 month', '+2 months'),
            'status' => 'draft',
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'published_at' => now(),
        ]);
    }
}
