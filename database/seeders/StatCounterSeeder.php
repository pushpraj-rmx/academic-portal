<?php

namespace Database\Seeders;

use App\Models\StatCounter;
use Illuminate\Database\Seeder;

class StatCounterSeeder extends Seeder
{
    public function run(): void
    {
        $counters = [
            ['label' => 'Teachers', 'value' => 60, 'sort_order' => 1],
            ['label' => 'Courses', 'value' => 40, 'sort_order' => 2],
            ['label' => 'Students', 'value' => 900, 'sort_order' => 3],
            ['label' => 'Satisfied Client', 'value' => 3675, 'sort_order' => 4],
        ];

        foreach ($counters as $counter) {
            StatCounter::query()->updateOrCreate(
                ['label' => $counter['label']],
                array_merge($counter, ['suffix' => '', 'icon' => null, 'is_published' => true])
            );
        }
    }
}
