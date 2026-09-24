<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Entry extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'type',
        'count',
        'was_here_before',
        'duration',
        'location_id',
        'commune_id',
        'material',
        'theme_id',
        'difficulty_id',
        'gender',
        'age_id',
        'statut_id',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'was_here_before' => 'boolean',
            'count' => 'integer',
            'duration' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Option::class, 'location_id');
    }

    public function commune(): BelongsTo
    {
        return $this->belongsTo(Option::class, 'commune_id');
    }

    public function theme(): BelongsTo
    {
        return $this->belongsTo(Option::class, 'theme_id');
    }

    public function difficulty(): BelongsTo
    {
        return $this->belongsTo(Option::class, 'difficulty_id');
    }

    public function age(): BelongsTo
    {
        return $this->belongsTo(Option::class, 'age_id');
    }

    public function statut(): BelongsTo
    {
        return $this->belongsTo(Option::class, 'statut_id');
    }

    public function persons(): HasMany
    {
        return $this->hasMany(EntryPerson::class);
    }
}
