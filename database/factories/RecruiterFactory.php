<?php

namespace Database\Factories;

use App\Models\Recruiter;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recruiter>
 */
class RecruiterFactory extends Factory
{
    protected $model = Recruiter::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $name = fake()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'website' => fake()->optional()->url(),
            'logo_path' => null,
            'contact_name' => fake()->optional()->name(),
            'contact_email' => fake()->optional()->companyEmail(),
            'contact_phone' => fake()->optional()->phoneNumber(),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }
}
