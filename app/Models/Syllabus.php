<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Syllabus extends Model
{
    /** @use HasFactory<\Database\Factories\SyllabusFactory> */
    use HasFactory;

    protected $table = 'syllabi';

    protected $fillable = [
        'course_id',
        'specialization_id',
        'academic_year',
        'version',
        'file_path',
        'is_active',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Syllabus $syllabus) {
            if ($syllabus->is_active && $syllabus->course_id) {
                static::query()
                    ->where('course_id', $syllabus->course_id)
                    ->when($syllabus->specialization_id, fn (Builder $q) => $q->where('specialization_id', $syllabus->specialization_id))
                    ->whereKeyNot($syllabus->getKey())
                    ->update(['is_active' => false]);
            }
        });
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function specialization(): BelongsTo
    {
        return $this->belongsTo(Specialization::class);
    }
}
