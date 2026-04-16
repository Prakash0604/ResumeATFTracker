<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ANALYSES TABLE
     * One-to-One with resumes. Stores all AI-generated scores.
     *
     * ATS Score Breakdown:
     *  - overall_score: Weighted composite (0–100)
     *  - keyword_score: How well keywords match job description
     *  - format_score: File format, section structure, parsability
     *  - content_score: Quality of descriptions, action verbs, metrics
     *  - skills_score: Relevant skills coverage
     *  - experience_score: Years, progression, relevance
     *  - education_score: Degree match, institution, GPA if present
     */
    public function up(): void
    {
        Schema::create('analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resume_id')->unique()->constrained()->cascadeOnDelete(); // 1:1

            // === SCORES (0–100 integers) ===
            $table->unsignedTinyInteger('overall_score')->default(0);
            $table->unsignedTinyInteger('keyword_score')->default(0);
            $table->unsignedTinyInteger('format_score')->default(0);
            $table->unsignedTinyInteger('content_score')->default(0);
            $table->unsignedTinyInteger('skills_score')->default(0);
            $table->unsignedTinyInteger('experience_score')->default(0);
            $table->unsignedTinyInteger('education_score')->default(0);

            // === KEYWORD ANALYSIS ===
            $table->json('matched_keywords')->nullable();    // ["Python","Docker","AWS"]
            $table->json('missing_keywords')->nullable();    // Keywords in JD but not resume
            $table->json('extra_keywords')->nullable();      // Resume has but not in JD
            $table->decimal('keyword_density', 5, 2)->default(0); // % of text that is keywords

            // === FORMAT ANALYSIS ===
            $table->boolean('has_contact_section')->default(false);
            $table->boolean('has_summary_section')->default(false);
            $table->boolean('has_experience_section')->default(false);
            $table->boolean('has_education_section')->default(false);
            $table->boolean('has_skills_section')->default(false);
            $table->boolean('has_projects_section')->default(false);
            $table->boolean('has_certifications_section')->default(false);
            $table->boolean('uses_tables')->default(false);     // Bad for ATS
            $table->boolean('uses_graphics')->default(false);   // Bad for ATS
            $table->boolean('uses_columns')->default(false);    // Risky for ATS
            $table->integer('page_count')->nullable();
            $table->integer('word_count')->nullable();
            $table->integer('character_count')->nullable();

            // === CONTENT ANALYSIS ===
            $table->json('action_verbs_found')->nullable();  // "Led","Built","Deployed"
            $table->integer('quantified_achievements')->default(0); // Count of numbers/metrics
            $table->boolean('has_dates')->default(false);    // Employment dates present
            $table->boolean('has_metrics')->default(false);  // "Increased by 30%"

            // === EXPERIENCE ===
            $table->integer('total_experience_years')->nullable();
            $table->integer('job_count')->nullable();
            $table->string('most_recent_title')->nullable();
            $table->string('most_recent_company')->nullable();
            $table->json('career_progression')->nullable();  // Array of job history

            // === EDUCATION ===
            $table->string('highest_degree')->nullable();    // "Bachelor's", "Master's"
            $table->string('field_of_study')->nullable();
            $table->string('institution')->nullable();
            $table->year('graduation_year')->nullable();

            // === ATS COMPATIBILITY ===
            $table->enum('ats_compatibility', ['excellent','good','fair','poor'])->default('fair');
            $table->json('ats_warnings')->nullable();        // Specific ATS issues

            // === AI METADATA ===
            $table->string('ai_model_used')->default('claude-sonnet-4-20250514');
            $table->integer('ai_tokens_used')->nullable();
            $table->decimal('analysis_duration_seconds', 8, 2)->nullable();

            $table->timestamps();

            $table->index('overall_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analyses');
    }
};
