<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Analysis Model
 * Stores all AI-computed scores for a resume.
 * One-to-One with Resume.
 */
class Analysis extends Model
{
    protected $fillable = [
        'resume_id', 'overall_score', 'keyword_score', 'format_score',
        'content_score', 'skills_score', 'experience_score', 'education_score',
        'matched_keywords', 'missing_keywords', 'extra_keywords', 'keyword_density',
        'has_contact_section', 'has_summary_section', 'has_experience_section',
        'has_education_section', 'has_skills_section', 'has_projects_section',
        'has_certifications_section', 'uses_tables', 'uses_graphics', 'uses_columns',
        'page_count', 'word_count', 'character_count',
        'action_verbs_found', 'quantified_achievements', 'has_dates', 'has_metrics',
        'total_experience_years', 'job_count', 'most_recent_title', 'most_recent_company',
        'career_progression', 'highest_degree', 'field_of_study', 'institution',
        'graduation_year', 'ats_compatibility', 'ats_warnings',
        'ai_model_used', 'ai_tokens_used', 'analysis_duration_seconds',
    ];

    protected $casts = [
        'matched_keywords'    => 'array',
        'missing_keywords'    => 'array',
        'extra_keywords'      => 'array',
        'action_verbs_found'  => 'array',
        'career_progression'  => 'array',
        'ats_warnings'        => 'array',
        'has_contact_section' => 'boolean',
        'has_summary_section' => 'boolean',
        'has_experience_section' => 'boolean',
        'has_education_section'  => 'boolean',
        'has_skills_section'     => 'boolean',
        'has_projects_section'   => 'boolean',
        'has_certifications_section' => 'boolean',
        'uses_tables'            => 'boolean',
        'uses_graphics'          => 'boolean',
        'uses_columns'           => 'boolean',
        'has_dates'              => 'boolean',
        'has_metrics'            => 'boolean',
    ];

    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }

    /**
     * Score breakdown as weighted array for radar chart rendering.
     * Weights: keywords=25%, content=20%, format=20%, skills=15%, experience=10%, education=10%
     */
    public function getScoreBreakdownAttribute(): array
    {
        return [
            ['label' => 'Keywords',   'score' => $this->keyword_score,    'weight' => 25, 'icon' => '🔑'],
            ['label' => 'Content',    'score' => $this->content_score,    'weight' => 20, 'icon' => '📝'],
            ['label' => 'Format',     'score' => $this->format_score,     'weight' => 20, 'icon' => '📋'],
            ['label' => 'Skills',     'score' => $this->skills_score,     'weight' => 15, 'icon' => '⚡'],
            ['label' => 'Experience', 'score' => $this->experience_score, 'weight' => 10, 'icon' => '💼'],
            ['label' => 'Education',  'score' => $this->education_score,  'weight' => 10, 'icon' => '🎓'],
        ];
    }

    /** ATS compatibility color */
    public function getCompatibilityColorAttribute(): string
    {
        return match ($this->ats_compatibility) {
            'excellent' => '#10b981',
            'good'      => '#3b82f6',
            'fair'      => '#f59e0b',
            'poor'      => '#ef4444',
            default     => '#6b7280',
        };
    }
}
