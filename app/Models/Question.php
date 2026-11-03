<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'section_id', 'direction_id', 'part', 'passage_id', 'question_text',
        'option_a', 'option_b', 'option_c', 'option_d',
        'option_a_audio', 'option_b_audio', 'option_c_audio', 'option_d_audio',
        'correct_answer', 'audio_path', 'order',
    ];

    /**
     * The attributes that should be hidden for serialization to prevent client-side answer sniffing.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'correct_answer',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function direction(): BelongsTo
    {
        return $this->belongsTo(Direction::class);
    }

    public function passage(): BelongsTo
    {
        return $this->belongsTo(Passage::class);
    }

    public function examKey(): HasOne
    {
        return $this->hasOne(ExamKey::class);
    }
}
