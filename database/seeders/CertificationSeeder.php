<?php

namespace Database\Seeders;

use App\Models\Certification;
use Illuminate\Database\Seeder;

class CertificationSeeder extends Seeder
{
    public function run(): void
    {
        $certifications = [
            ['name' => 'CDG & DAC', 'sort_order' => 1],
            ['name' => 'JAS-ANZ & IAF', 'sort_order' => 2],
            ['name' => 'IEEE', 'sort_order' => 3],
        ];

        foreach ($certifications as $certification) {
            Certification::query()->updateOrCreate(
                ['name' => $certification['name']],
                array_merge($certification, ['image' => null, 'is_published' => true])
            );
        }
    }
}
