<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntryCustomValue extends Model
{
    protected $table = 'entry_custom_values';

    protected $fillable = ['entry_id', 'custom_field_id', 'value'];

    public function entry(): BelongsTo
    {
        return $this->belongsTo(Entry::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(CustomField::class, 'custom_field_id');
    }
}
