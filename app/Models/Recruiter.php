<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recruiter extends Model
{
    /** @use HasFactory<\Database\Factories\RecruiterFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'website',
        'logo_path',
        'contact_name',
        'contact_email',
        'contact_phone',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function placements(): HasMany
    {
        return $this->hasMany(Placement::class);
    }
}
