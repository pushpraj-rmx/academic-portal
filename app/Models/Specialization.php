<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Specialization extends Model
{
    /** @use HasFactory<\Database\Factories\SpecializationFactory> */
    use HasFactory;

    protected $fillable = [
        'course_id',
        'name',
        'slug',
        'description',
        'industry_relevance',
        'career_outcomes',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope('ordered', function (Builder $query) {
            $query->orderBy('sort_order')->orderBy('name');
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function syllabi(): HasMany
    {
        return $this->hasMany(Syllabus::class);
    }
}
