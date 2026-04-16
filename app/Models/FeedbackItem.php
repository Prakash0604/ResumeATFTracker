<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackItem extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    // protected $table = 'feedback_item';

    protected $fillable = [
        'resume_id', 'priority', 'category', 'title', 'description',
        'suggestion', 'example', 'is_addressed', 'addressed_at',
    ];

    protected $casts = [
        'is_addressed'  => 'boolean',
        'addressed_at'  => 'datetime',
    ];

    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'critical' => '#ef4444',
            'high'     => '#f97316',
            'medium'   => '#eab308',
            'low'      => '#22c55e',
            default    => '#6b7280',
        };
    }

    public function getPriorityIconAttribute(): string
    {
        return match ($this->priority) {
            'critical' => '🚨',
            'high'     => '⚠️',
            'medium'   => '💡',
            'low'      => '✨',
            default    => '📌',
        };
    }
}
