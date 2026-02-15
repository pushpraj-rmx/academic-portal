<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'course_id' => Course::factory(),
            'enrollment_id' => fake()->unique()->numerify('ENR########'),
            'roll_number' => fake()->numerify('ROLL###'),
            'phone' => fake()->optional()->phoneNumber(),
            'alternate_phone' => null,
            'verification_status' => 'pending',
            'verified_at' => null,
            'verified_by' => null,
            'notes' => null,
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => 'verified',
            'verified_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => 'rejected',
            'verified_at' => null,
        ]);
    }
}
