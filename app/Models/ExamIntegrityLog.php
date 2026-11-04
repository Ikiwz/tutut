<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamIntegrityLog extends Model
{
    protected $fillable = [
        'exam_attempt_id',
        'event_type',
        'severity',
        'title',
        'description',
        'section_slug',
        'question_number',
        'metadata',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'recorded_at' => 'datetime',
            'question_number' => 'integer',
        ];
    }

    public function examAttempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class);
    }

    public function severityColor(): string
    {
        return match ($this->severity) {
            'severe' => 'danger',
            'medium' => 'warning',
            default => 'zinc',
        };
    }

    public function severityLabel(): string
    {
        return match ($this->severity) {
            'severe' => 'Severe (Berat)',
            'medium' => 'Medium (Sedang)',
            default => 'Minor (Sepele / Ringan)',
        };
    }
}
