<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Keyword extends Model
{
    protected $fillable = [
        'resume_id', 'word', 'frequency',
        'is_ats_keyword', 'is_in_job_description',
    ];

    protected $casts = [
        'is_ats_keyword'        => 'boolean',
        'is_in_job_description' => 'boolean',
        'frequency'             => 'integer',
    ];

    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }
}
