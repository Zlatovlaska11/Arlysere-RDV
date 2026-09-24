<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $fillable = ['type', 'value', 'color', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type)->orderBy('sort_order')->orderBy('value');
    }

    public function scopeLocations(Builder $query): Builder
    {
        return $query->ofType('location');
    }

    public function scopeCommunes(Builder $query): Builder
    {
        return $query->ofType('commune');
    }

    public function scopeThemes(Builder $query): Builder
    {
        return $query->ofType('theme');
    }

    public function scopeDifficulties(Builder $query): Builder
    {
        return $query->ofType('difficulty');
    }

    public function scopeAges(Builder $query): Builder
    {
        return $query->ofType('age');
    }

    public function scopeStatuts(Builder $query): Builder
    {
        return $query->ofType('statut');
    }
}
