<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestSession extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'description', 'access_code', 'capacity', 'is_active', 'starts_at', 'ends_at'];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function isLocked(): bool
    {
        return $this->starts_at !== null && $this->starts_at->isFuture();
    }

    public function isEnded(): bool
    {
        return $this->ends_at !== null && $this->ends_at->isPast();
    }

    public function isOpen(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->isLocked()) {
            return false;
        }

        if ($this->isEnded()) {
            return false;
        }

        return true;
    }

    public function bookedSeatsCount(): int
    {
        return $this->examAttempts()->count();
    }

    public function remainingSeats(): ?int
    {
        if (is_null($this->capacity)) {
            return null;
        }

        return max(0, $this->capacity - $this->bookedSeatsCount());
    }

    public function isFull(): bool
    {
        if (is_null($this->capacity)) {
            return false;
        }

        return $this->remainingSeats() <= 0;
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
