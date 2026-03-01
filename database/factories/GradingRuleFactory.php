<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GradingRule>
 */
class GradingRuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $minPercentage = fake()->numberBetween(40, 90);
        $maxPercentage = min(100, $minPercentage + fake()->numberBetween(5, 10));

        return [
            'grade' => fake()->randomElement(['A+', 'A', 'B+', 'B', 'C']),
            'min_percentage' => $minPercentage,
            'max_percentage' => $maxPercentage,
            'description' => fake()->sentence(),
            'gpa' => fake()->randomFloat(2, 5, 10),
            'sort_order' => fake()->numberBetween(0, 20),
        ];
    }
}
