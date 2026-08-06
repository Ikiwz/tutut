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

    private function getConvertedScore(string $sectionSlug, int $correctCount): int
    {
        // Tabel konversi TOEFL PBT (berdasarkan referensi umum dan gambar yang Anda berikan)
        // Jika ada nilai yang kurang tepat, Anda bisa menyesuaikan angka di dalam array ini.
        $conversionTables = [
            'listening' => [
                50=>68, 49=>67, 48=>66, 47=>65, 46=>63, 45=>62, 44=>61, 43=>60, 42=>59, 41=>58,
                40=>57, 39=>57, 38=>56, 37=>55, 36=>54, 35=>54, 34=>53, 33=>53, 32=>52, 31=>52,
                30=>51, 29=>50, 28=>49, 27=>49, 26=>48, 25=>48, 24=>47, 23=>47, 22=>46, 21=>45,
                20=>45, 19=>44, 18=>43, 17=>42, 16=>41, 15=>41, 14=>39, 13=>38, 12=>37, 11=>35,
                10=>33, 9=>32, 8=>32, 7=>31, 6=>30, 5=>29, 4=>28, 3=>27, 2=>26, 1=>25, 0=>24
            ],
            'structure' => [
                40=>68, 39=>68, 38=>65, 37=>63, 36=>61, 35=>60, 34=>58, 33=>57, 32=>56, 31=>55,
                30=>54, 29=>53, 28=>52, 27=>51, 26=>50, 25=>49, 24=>48, 23=>47, 22=>46, 21=>45,
                20=>44, 19=>43, 18=>42, 17=>41, 16=>40, 15=>40, 14=>38, 13=>37, 12=>36, 11=>35,
                10=>33, 9=>31, 8=>29, 7=>27, 6=>26, 5=>25, 4=>23, 3=>22, 2=>21, 1=>20, 0=>20
            ],
            'reading' => [
                50=>67, 49=>66, 48=>65, 47=>63, 46=>61, 45=>60, 44=>59, 43=>58, 42=>57, 41=>56,
                40=>55, 39=>54, 38=>54, 37=>53, 36=>52, 35=>52, 34=>51, 33=>50, 32=>49, 31=>48,
                30=>48, 29=>47, 28=>46, 27=>46, 26=>45, 25=>44, 24=>43, 23=>43, 22=>42, 21=>41,
                20=>40, 19=>39, 18=>38, 17=>37, 16=>36, 15=>35, 14=>34, 13=>33, 12=>32, 11=>31,
                10=>30, 9=>29, 8=>28, 7=>28, 6=>27, 5=>26, 4=>25, 3=>23, 2=>23, 1=>22, 0=>21
            ],
        ];

        if (isset($conversionTables[$sectionSlug]) && isset($conversionTables[$sectionSlug][$correctCount])) {
            return $conversionTables[$sectionSlug][$correctCount];
        }

        return 0;
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
            
            // Dapatkan nilai konversi berdasarkan tabel
            $score = $this->getConvertedScore($section->slug, $correct);

            match ($section->slug) {
                'listening' => $this->score_listening = $score,
                'structure' => $this->score_structure = $score,
                'reading' => $this->score_reading = $score,
                default => null,
            };
        }

        // Ambil nilai dari masing-masing section (default ke 0 jika belum ada)
        $scoreListening = $this->score_listening ?? 0;
        $scoreStructure = $this->score_structure ?? 0;
        $scoreReading = $this->score_reading ?? 0;

        // Rumus TOEFL: (Listening + Structure + Reading) * 10 / 3
        $totalConverted = $scoreListening + $scoreStructure + $scoreReading;
        $this->total_score = (int) round(($totalConverted * 10) / 3);
        
        // Jika tidak ada satu pun jawaban yang benar (atau kosong), set skor ke 0
        $totalCorrectAnswers = $this->answers()->where('is_correct', true)->count();
        if ($totalCorrectAnswers === 0) {
            $this->score_listening = 0;
            $this->score_structure = 0;
            $this->score_reading = 0;
            $this->total_score = 0;
        }
        
        $this->status = 'completed';
        $this->completed_at = now();
        $this->save();
    }
}
