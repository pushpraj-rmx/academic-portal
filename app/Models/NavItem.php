<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class NavItem extends Model
{
    protected $fillable = [
        'parent_id',
        'label',
        'route_name',
        'route_params',
        'url',
        'sort_order',
        'is_visible',
        'show_when',
        'dynamic_source',
    ];

    protected function casts(): array
    {
        return [
            'route_params' => 'array',
            'is_visible' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(NavItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(NavItem::class, 'parent_id');
    }

    public function getHrefAttribute(): ?string
    {
        if ($this->route_name) {
            $params = $this->route_params ?? [];

            return Route::has($this->route_name)
                ? route($this->route_name, $params)
                : null;
        }

        return $this->url;
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function scopeForHeader(Builder $query): Builder
    {
        return $query->roots()->visible()->ordered()->with([
            'children' => fn ($q) => $q->visible()->ordered(),
        ]);
    }

    public function isDropdown(): bool
    {
        return $this->dynamic_source !== null || $this->children->isNotEmpty();
    }

    public function isActive(?Request $request = null): bool
    {
        $request = $request ?? request();
        if ($this->url) {
            return $request->fullUrl() === $this->href || $request->is(ltrim($this->url, '/'));
        }
        if ($this->route_name && $request->route()) {
            $currentName = $request->route()->getName();
            if ($currentName !== $this->route_name) {
                return false;
            }
            $params = $this->route_params ?? [];
            foreach ($params as $key => $value) {
                if ($request->route($key) != $value) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    public function shouldShow(): bool
    {
        if ($this->show_when === null) {
            return true;
        }

        return match ($this->show_when) {
            'guest' => auth()->guest(),
            'auth' => auth()->check(),
            'auth_admin' => auth()->check() && ! $this->isStudentOnly(),
            default => true,
        };
    }

    private function isStudentOnly(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }
        $roleNames = $user->getRoleNames();

        return $roleNames->count() === 1 && $roleNames->contains(\App\Enums\UserRole::Student->value);
    }
}
