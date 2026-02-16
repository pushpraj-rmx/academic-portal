<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamSessionSubject extends Model
{
    /** @use HasFactory<\Database\Factories\ExamSessionSubjectFactory> */
    use HasFactory;

    protected $fillable = [
        'exam_session_id',
        'subject_id',
        'exam_date',
        'exam_time',
    ];

    protected function casts(): array
    {
        return [
            'exam_date' => 'date',
        ];
    }

    public function examSession(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
