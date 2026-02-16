<?php

namespace Database\Factories;

use App\Models\Placement;
use App\Models\Recruiter;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Placement>
 */
class PlacementFactory extends Factory
{
    protected $model = Placement::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $year = fake()->numberBetween(2022, 2026);
        $academicYear = "{$year}-".($year + 1);

        return [
            'student_id' => Student::factory(),
            'recruiter_id' => Recruiter::factory(),
            'academic_year' => $academicYear,
            'placement_type' => fake()->randomElement(['job', 'internship']),
            'designation' => fake()->optional()->jobTitle(),
            'package_amount' => fake()->optional()->randomFloat(2, 3, 25),
            'status' => 'offered',
            'offer_date' => fake()->optional()->date(),
        ];
    }
}
