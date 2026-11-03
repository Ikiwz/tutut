<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'correct_key',
    ];

    /**
     * @return BelongsTo<Question, ExamKey>
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
