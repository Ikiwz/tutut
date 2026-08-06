<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    protected $fillable = [
        'name', 'slug', 'description',
        'directions_audio_a', 'directions_audio_b', 'directions_audio_c',
        'duration_minutes', 'order',
    ];

    public function directions(): HasMany
    {
        return $this->hasMany(Direction::class)->orderBy('order');
    }

    public function passages(): HasMany
    {
        return $this->hasMany(Passage::class)->orderBy('order');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function testSessions()
    {
        return $this->belongsToMany(TestSession::class, 'test_session_sections');
    }
}
