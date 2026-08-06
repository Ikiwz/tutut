<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamAttempt extends Model
{
    protected $fillable = [
        'user_id', 'test_session_id', 'current_section_slug',
        'current_question_index', 'started_at', 'completed_at',
        'score_listening', 'score_structure', 'score_reading',
        'total_score', 'status',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function testSession(): BelongsTo
    {
        return $this->belongsTo(TestSession::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ExamAnswer::class);
    }

    public function calculateScores(): void
    {
        $sections = $this->testSession->sections;

        foreach ($sections as $section) {
            $questionIds = $section->questions()->pluck('id');
            $correct = $this->answers()
                ->whereIn('question_id', $questionIds)
                ->where('is_correct', true)
                ->count();
            $total = $questionIds->count();

            // TOEFL PBT-like scoring: scale to section score range
            $score = $total > 0 ? round(($correct / $total) * 68) : 0;

            match ($section->slug) {
                'listening' => $this->score_listening = $score,
                'structure' => $this->score_structure = $score,
                'reading' => $this->score_reading = $score,
                default => null,
            };
        }

        $scores = array_filter([$this->score_listening, $this->score_structure, $this->score_reading]);
        $this->total_score = count($scores) > 0 ? (int) round((array_sum($scores) / count($scores)) * 10) : 0;
        $this->status = 'completed';
        $this->completed_at = now();
        $this->save();
    }
}
