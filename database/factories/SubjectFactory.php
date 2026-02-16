<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subject>
 */
class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $course = Course::factory()->create();

        return [
            'course_id' => $course->id,
            'name' => fake()->words(3, true),
            'code' => 'SUB'.$course->id.fake()->numberBetween(100, 999),
            'max_marks' => fake()->randomElement([50, 75, 100]),
            'passing_marks' => fake()->numberBetween(18, 35),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function forCourse(Course $course): static
    {
        return $this->state(fn (array $attributes) => [
            'course_id' => $course->id,
            'code' => 'SUB'.$course->id.fake()->numberBetween(100, 999),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }
}
