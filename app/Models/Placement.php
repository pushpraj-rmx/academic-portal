<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Placement extends Model
{
    /** @use HasFactory<\Database\Factories\PlacementFactory> */
    use HasFactory;

    protected $fillable = [
        'student_id',
        'recruiter_id',
        'academic_year',
        'placement_type',
        'designation',
        'package_amount',
        'status',
        'offer_date',
    ];

    protected function casts(): array
    {
        return [
            'package_amount' => 'decimal:2',
            'offer_date' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function recruiter(): BelongsTo
    {
        return $this->belongsTo(Recruiter::class);
    }
}
