<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class CourseCategory extends Model
{
    /** @use HasFactory<\Database\Factories\CourseCategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image_path',
        'image_alt',
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

        static::deleting(function (CourseCategory $category): void {
            $count = $category->courses()->count();
            if ($count > 0) {
                throw ValidationException::withMessages([
                    'category' => ["Cannot delete this category because it has {$count} course(s). Reassign or delete the courses first."],
                ]);
            }
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'course_category_id');
    }
}
