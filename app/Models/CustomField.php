<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomField extends Model
{
    protected $fillable = ['label', 'type', 'is_optional', 'is_active', 'sort_order'];

    protected $casts = ['is_optional' => 'boolean', 'is_active' => 'boolean'];

    public function options(): HasMany
    {
        return $this->hasMany(CustomFieldOption::class)->orderBy('sort_order');
    }

    public function values(): HasMany
    {
        return $this->hasMany(EntryCustomValue::class);
    }

    public function scopeActive($query): void
    {
        $query->where('is_active', true)->orderBy('sort_order');
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'list'  => 'Liste',
            'open'  => 'Texte libre',
            'yesno' => 'Oui / Non',
            default => $this->type,
        };
    }
}
