<?php

namespace Database\Seeders;

use App\Models\GradingRule;
use Illuminate\Database\Seeder;

class GradingRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            ['grade' => 'A+', 'min_percentage' => 90, 'max_percentage' => 100, 'gpa' => 10, 'sort_order' => 1],
            ['grade' => 'A', 'min_percentage' => 80, 'max_percentage' => 89.99, 'gpa' => 9, 'sort_order' => 2],
            ['grade' => 'B+', 'min_percentage' => 70, 'max_percentage' => 79.99, 'gpa' => 8, 'sort_order' => 3],
            ['grade' => 'B', 'min_percentage' => 60, 'max_percentage' => 69.99, 'gpa' => 7, 'sort_order' => 4],
            ['grade' => 'C', 'min_percentage' => 50, 'max_percentage' => 59.99, 'gpa' => 6, 'sort_order' => 5],
            ['grade' => 'D', 'min_percentage' => 40, 'max_percentage' => 49.99, 'gpa' => 5, 'sort_order' => 6],
        ];

        foreach ($rules as $rule) {
            GradingRule::query()->updateOrCreate(
                ['grade' => $rule['grade']],
                array_merge($rule, ['description' => null])
            );
        }
    }
}
