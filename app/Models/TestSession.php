<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestSession extends Model
{
    protected $fillable = ['title', 'description', 'is_active', 'starts_at', 'ends_at'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(Section::class, 'test_session_sections')->orderBy('sections.order');
    }

    public function examAttempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }
}
