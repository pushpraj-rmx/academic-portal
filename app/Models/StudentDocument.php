<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentDocument extends Model
{
    /** @use HasFactory<\Database\Factories\StudentDocumentFactory> */
    use HasFactory;

    protected $fillable = [
        'student_id',
        'type',
        'file_path',
        'original_name',
        'verified',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'verified' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
