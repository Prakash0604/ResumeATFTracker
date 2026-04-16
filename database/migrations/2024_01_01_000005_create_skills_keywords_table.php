<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * SKILLS TABLE
     * Individual skills extracted from the resume.
     * Normalized so we can query & compare across resumes.
     *
     * proficiency: inferred from context (years, project depth, certifications)
     * category: technical, soft, language, tool, framework, etc.
     */
    public function up(): void
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resume_id')->constrained()->cascadeOnDelete();

            $table->string('name', 100);                    // "Python", "Leadership", "MySQL"
            $table->enum('category', [
                'technical',     // Programming languages, algorithms
                'framework',     // Laravel, React, Django
                'tool',          // Git, Docker, Figma
                'soft',          // Communication, teamwork
                'language',      // English, Spanish (spoken languages)
                'certification', // AWS Certified, PMP
                'domain',        // Finance, Healthcare (domain knowledge)
                'other'
            ])->default('technical');

            $table->enum('proficiency', [
                'expert',        // 5+ years, lead projects
                'advanced',      // 3-5 years
                'intermediate',  // 1-3 years
                'beginner',      // <1 year
                'unknown'        // Cannot infer
            ])->default('unknown');

            $table->boolean('is_in_job_description')->default(false); // Matched to JD
            $table->integer('mention_count')->default(1);  // How many times it appears

            $table->timestamps();

            $table->index(['resume_id', 'category']);
            $table->index('name');
        });

        /**
         * KEYWORDS TABLE
         * Stores the full keyword frequency map extracted from resume text.
         * Used for ATS matching algorithm and keyword density calculation.
         */
        Schema::create('keywords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resume_id')->constrained()->cascadeOnDelete();

            $table->string('word', 100);
            $table->integer('frequency')->default(1);       // Occurrence count
            $table->boolean('is_ats_keyword')->default(false); // High-value ATS keyword
            $table->boolean('is_in_job_description')->default(false);

            $table->timestamps();

            $table->unique(['resume_id', 'word']);
            $table->index(['resume_id', 'is_ats_keyword']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keywords');
        Schema::dropIfExists('skills');
    }
};
