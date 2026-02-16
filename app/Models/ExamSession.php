<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamSession extends Model
{
    /** @use HasFactory<\Database\Factories\ExamSessionFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'academic_year',
        'session_type',
        'start_date',
        'end_date',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'published_at' => 'datetime',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function examSessionSubjects(): HasMany
    {
        return $this->hasMany(ExamSessionSubject::class);
    }

    public function examForms(): HasMany
    {
        return $this->hasMany(ExamForm::class);
    }

    public function subjectMarks(): HasMany
    {
        return $this->hasMany(SubjectMark::class);
    }
}
