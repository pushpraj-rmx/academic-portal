<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class StudentsArrayImport implements ToCollection
{
    /**
     * @var array<int, array<int, mixed>>
     */
    public array $rows = [];

    public function collection(Collection $rows): void
    {
        $this->rows = $rows->map(fn ($row) => $row->toArray())->all();
    }
}

