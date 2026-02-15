<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentDocument>
 */
class StudentDocumentFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        $type = fake()->randomElement(['photo', 'id_proof', 'marksheet_10', 'marksheet_12', 'other']);

        return [
            'student_id' => Student::factory(),
            'type' => $type,
            'file_path' => 'student-documents/'.fake()->uuid().'.pdf',
            'original_name' => fake()->optional()->word().'.pdf',
            'verified' => false,
            'verified_at' => null,
        ];
    }
}
