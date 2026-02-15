<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Syllabus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Syllabus>
 */
class SyllabusFactory extends Factory
{
    protected $model = Syllabus::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'specialization_id' => null,
            'academic_year' => fake()->optional()->regexify('20[2-3][0-9]-[2-3][0-9]'),
            'version' => fake()->optional()->regexify('v[1-9]'),
            'file_path' => 'syllabus/'.fake()->uuid().'.pdf',
            'is_active' => true,
            'published_at' => fake()->optional()->dateTimeThisYear(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
            'published_at' => null,
        ]);
    }
}
