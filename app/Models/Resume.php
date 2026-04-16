<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Resume extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'original_filename', 'stored_filename', 'file_path',
        'file_type', 'file_size', 'raw_text', 'detected_language',
        'candidate_name', 'candidate_email', 'candidate_phone',
        'candidate_location', 'candidate_linkedin', 'candidate_github',
        'target_job_title', 'target_job_description',
        'status', 'processing_error', 'analyzed_at',
    ];

    protected $casts = [
        'analyzed_at' => 'datetime',
        'file_size'   => 'integer',
    ];

   
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function analysis(): HasOne
    {
        return $this->hasOne(Analysis::class);
    }

    public function feedback_items(): HasMany
    {
        return $this->hasMany(FeedbackItem::class)->orderByRaw(
            "CASE priority WHEN 'critical' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 WHEN 'low' THEN 4 ELSE 5 END"
        );
    }

    public function criticalFeedbackItems(): HasMany
    {
        return $this->hasMany(FeedbackItem::class)
            ->whereIn('priority', ['critical', 'high']);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class);
    }

    public function keywords(): HasMany
    {
        return $this->hasMany(Keyword::class);
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }

    public function getScoreColorAttribute(): string
    {
        $score = $this->analysis?->overall_score ?? 0;
        return match (true) {
            $score >= 80 => 'success',
            $score >= 60 => 'warning',
            $score >= 40 => 'orange',
            default      => 'danger',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'uploaded'   => '⏳ Queued',
            'processing' => '🔄 Analyzing',
            'analyzed'   => '✅ Complete',
            'failed'     => '❌ Failed',
            default      => 'Unknown',
        };
    }


    public function scopeAnalyzed($query)
    {
        return $query->where('status', 'analyzed');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['uploaded', 'processing']);
    }
}
