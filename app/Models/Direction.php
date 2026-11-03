<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Direction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'section_id', 'label', 'title', 'description', 'audio_path', 'order',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }
}
