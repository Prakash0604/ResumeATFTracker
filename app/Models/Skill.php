<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Skill extends Model
{
    protected $fillable = [
        'resume_id', 'name', 'category', 'proficiency',
        'is_in_job_description', 'mention_count',
    ];

    protected $casts = [
        'is_in_job_description' => 'boolean',
        'mention_count'         => 'integer',
    ];

    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }

    public function getCategoryIconAttribute(): string
    {
        return match ($this->category) {
            'technical'     => '⚙️',
            'framework'     => '🏗️',
            'tool'          => '🔧',
            'soft'          => '🤝',
            'language'      => '🌐',
            'certification' => '🏆',
            'domain'        => '📊',
            default         => '📌',
        };
    }
}
