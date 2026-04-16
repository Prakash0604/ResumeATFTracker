<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * RESUMES TABLE
     * Core entity — stores uploaded resume files & extracted metadata.
     * Belongs to a USER. Has ONE ANALYSIS. Has MANY FEEDBACKS.
     *
     * Flow: Upload → parse text → store → dispatch job → analysis created
     */
    public function up(): void
    {
        Schema::create('resumes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // FK → users

            // File Storage
            $table->string('original_filename');             // "John_Doe_Resume.pdf"
            $table->string('stored_filename');               // UUID-based secure name
            $table->string('file_path');                     // Storage path
            $table->string('file_type', 10);                 // pdf | docx | txt
            $table->integer('file_size');                    // Bytes

            // Extracted Content
            $table->longText('raw_text')->nullable();        // Full extracted text from PDF/DOCX
            $table->string('detected_language', 10)->default('en'); // ISO language code

            // Candidate Info (AI-parsed from resume)
            $table->string('candidate_name')->nullable();
            $table->string('candidate_email')->nullable();
            $table->string('candidate_phone')->nullable();
            $table->string('candidate_location')->nullable();
            $table->string('candidate_linkedin')->nullable();
            $table->string('candidate_github')->nullable();

            // Target Job (user-provided for keyword matching)
            $table->string('target_job_title')->nullable();
            $table->text('target_job_description')->nullable();

            // Processing Status
            $table->enum('status', [
                'uploaded',      // Just received file
                'processing',    // Job dispatched, AI running
                'analyzed',      // Analysis complete
                'failed'         // Something went wrong
            ])->default('uploaded');

            $table->text('processing_error')->nullable();    // Error message if failed
            $table->timestamp('analyzed_at')->nullable();    // When analysis finished

            $table->timestamps();
            $table->softDeletes();                           // Soft delete for recovery

            $table->index(['user_id', 'status']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resumes');
    }
};
