<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectMark extends Model
{
    /** @use HasFactory<\Database\Factories\SubjectMarkFactory> */
    use HasFactory;

    protected $fillable = [
        'exam_session_id',
        'student_id',
        'subject_id',
        'marks_obtained',
        'is_absent',
    ];

    protected function casts(): array
    {
        return [
            'marks_obtained' => 'decimal:2',
            'is_absent' => 'boolean',
        ];
    }

    /**
     * Check if a subject mark already exists for the given session/student/subject.
     */
    public static function existsFor(int $examSessionId, int $studentId, int $subjectId): bool
    {
        return static::query()
            ->where('exam_session_id', $examSessionId)
            ->where('student_id', $studentId)
            ->where('subject_id', $subjectId)
            ->exists();
    }

    /**
     * @return array<int>
     */
    public static function subjectIdsForStudentInSession(int $examSessionId, int $studentId): array
    {
        return static::query()
            ->where('exam_session_id', $examSessionId)
            ->where('student_id', $studentId)
            ->pluck('subject_id')
            ->map(static fn ($id): int => (int) $id)
            ->all();
    }

    public function examSession(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
