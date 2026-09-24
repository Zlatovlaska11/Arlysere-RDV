<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntryPerson extends Model
{
    protected $table = 'entry_persons';

    protected $fillable = ['entry_id', 'gender', 'age_id', 'statut_id', 'was_here_before'];

    protected $casts = ['was_here_before' => 'boolean'];

    public function entry(): BelongsTo
    {
        return $this->belongsTo(Entry::class);
    }

    public function age(): BelongsTo
    {
        return $this->belongsTo(Option::class, 'age_id');
    }

    public function statut(): BelongsTo
    {
        return $this->belongsTo(Option::class, 'statut_id');
    }
}
