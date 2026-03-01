<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradingRule extends Model
{
    /** @use HasFactory<\Database\Factories\GradingRuleFactory> */
    use HasFactory;

    protected $fillable = [
        'grade',
        'min_percentage',
        'max_percentage',
        'description',
        'gpa',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'min_percentage' => 'decimal:2',
            'max_percentage' => 'decimal:2',
            'gpa' => 'decimal:2',
        ];
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('min_percentage');
    }
}
