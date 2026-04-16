<?php

namespace App\Services;

use App\Models\Resume;
use App\Models\Analysis;
use App\Models\FeedbackItem;
use App\Models\Skill;
use App\Models\Keyword;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * ResumeAnalyzerService
 *
 * The heart of the ATF system. Orchestrates full resume analysis pipeline:
 *
 *  1. extractText()        — Pull raw text from PDF/DOCX
 *  2. callClaudeAPI()      — Send to Anthropic for AI analysis
 *  3. parseAIResponse()    — Decode JSON from AI
 *  4. persistAnalysis()    — Save scores to `analyses` table
 *  5. persistFeedbacks()   — Save actionable suggestions to `feedbacks` table
 *  6. persistSkills()      — Save extracted skills to `skills` table
 *  7. persistKeywords()    — Build keyword frequency map in `keywords` table
 */
class ResumeAnalyzerService
{
    private const CLAUDE_API_URL = 'https://api.anthropic.com/v1/messages';
    private const MODEL = 'claude-sonnet-4-20250514';
    private const MAX_TOKENS = 4096;

    public function __construct(
        private readonly TextExtractorService $extractor
    ) {}

    /**
     * Main entry point — called from ProcessResumeJob
     *
     * @param Resume $resume
     * @throws \Exception on unrecoverable failure
     */
    public function analyze(Resume $resume): void
    {
        $startTime = microtime(true);

        // Step 1: Extract text if not already done
        if (empty($resume->raw_text)) {
            $rawText = $this->extractor->extract(
                Storage::disk('local')->path($resume->file_path),
                $resume->file_type
            );
            $resume->update(['raw_text' => $rawText]);
        }

        // Step 2: Call Claude AI for analysis
        $aiResponse = $this->callClaudeAPI($resume);

        // Step 3: Parse JSON response
        $analysisData = $this->parseAIResponse($aiResponse);

        $duration = round(microtime(true) - $startTime, 2);

        // Step 4-7: Persist all results in a transaction
        \DB::transaction(function () use ($resume, $analysisData, $duration, $aiResponse) {
            $this->persistAnalysis($resume, $analysisData, $duration, $aiResponse);
            $this->persistFeedbackItems($resume, $analysisData['feedbacks'] ?? []);
            $this->persistSkills($resume, $analysisData['skills'] ?? []);
            $this->persistKeywords($resume, $analysisData['keywords'] ?? []);
        });

        $resume->update([
            'status'      => 'analyzed',
            'analyzed_at' => now(),
        ]);
    }

    /**
     * Build the AI prompt and call the Anthropic API.
     * Returns the raw response array.
     */
    private function callClaudeAPI(Resume $resume): array
    {
        $prompt = $this->buildPrompt($resume);

        $requestData = [
            "model" => env('OPENROUTER_MODEL', "google/gemma-4-31b-it:free"),
            "messages" => [
                ["role" => "user", "content" => $prompt]
            ]
        ];
        
        Log::info('API Request', ['data' => $requestData]);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
            'Content-Type' => 'application/json'
        ])->timeout(120)->post(env('OPENROUTER_URL'), $requestData);

        Log::info('API Response', [
            'status' => $response->status(),
            'body' => $response->body(),
            'json' => $response->json()
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException(
                'Claude API error: ' . $response->status() . ' — ' . $response->body()
            );
        }

        return $response->json();
    }

    /**
     * Construct the detailed system + user prompt for Claude.
     * The prompt instructs Claude to return pure JSON matching our schema.
     */
    private function buildPrompt(Resume $resume): string
    {
        $jobContext = '';
        if ($resume->target_job_title) {
            $jobContext = "\n\nTARGET JOB TITLE: {$resume->target_job_title}";
        }
        if ($resume->target_job_description) {
            $jobContext .= "\n\nJOB DESCRIPTION:\n" . substr($resume->target_job_description, 0, 2000);
        }

        $resumeText = substr($resume->raw_text ?? '', 0, 6000);

        return <<<PROMPT
You are an expert ATS (Applicant Tracking System) resume analyzer and career coach with 20 years of recruiting experience across tech, finance, and healthcare industries.

Analyze the following resume and return ONLY a valid JSON object — no markdown, no backticks, no explanations.{$jobContext}

RESUME TEXT:
---
{$resumeText}
---

Return this exact JSON structure:
{
  "scores": {
    "overall": 0-100,
    "keyword": 0-100,
    "format": 0-100,
    "content": 0-100,
    "skills": 0-100,
    "experience": 0-100,
    "education": 0-100
  },
  "candidate": {
    "name": "string or null",
    "email": "string or null",
    "phone": "string or null",
    "location": "string or null",
    "linkedin": "string or null",
    "github": "string or null"
  },
  "keywords": {
    "matched": ["array of keywords found in both resume and JD"],
    "missing": ["array of important keywords from JD missing in resume"],
    "extra": ["additional good keywords in resume"],
    "density": 0.0-100.0,
    "top_words": [{"word": "Python", "frequency": 5, "is_ats_keyword": true}]
  },
  "format": {
    "has_contact": true/false,
    "has_summary": true/false,
    "has_experience": true/false,
    "has_education": true/false,
    "has_skills": true/false,
    "has_projects": true/false,
    "has_certifications": true/false,
    "uses_tables": true/false,
    "uses_graphics": true/false,
    "uses_columns": true/false,
    "page_count": integer,
    "word_count": integer,
    "ats_compatibility": "excellent|good|fair|poor",
    "ats_warnings": ["list of specific ATS issues"]
  },
  "content": {
    "action_verbs": ["Led","Built","Designed"],
    "quantified_achievements": integer,
    "has_dates": true/false,
    "has_metrics": true/false
  },
  "experience": {
    "total_years": integer or null,
    "job_count": integer,
    "most_recent_title": "string or null",
    "most_recent_company": "string or null",
    "career_progression": [{"title":"","company":"","start":"","end":"","duration_months": 12}]
  },
  "education": {
    "highest_degree": "Bachelor's|Master's|PhD|Associate's|High School|Other|null",
    "field_of_study": "string or null",
    "institution": "string or null",
    "graduation_year": integer or null
  },
  "skills": [
    {
      "name": "Python",
      "category": "technical|framework|tool|soft|language|certification|domain|other",
      "proficiency": "expert|advanced|intermediate|beginner|unknown",
      "is_in_job_description": true/false,
      "mention_count": integer
    }
  ],
  "feedbacks": [
    {
      "priority": "critical|high|medium|low",
      "category": "contact|summary|keywords|experience|education|skills|format|content|length|ats|achievements|spelling|other",
      "title": "Short actionable title (max 60 chars)",
      "description": "Why this is important for ATS and hiring managers",
      "suggestion": "Exact specific action the candidate should take",
      "example": "Optional before/after example or template"
    }
  ]
}

Scoring rubric:
- overall: weighted average (keywords 25%, content 20%, format 20%, skills 15%, experience 10%, education 10%)
- keyword: % of JD keywords matched in resume (or general ATS keyword presence if no JD)
- format: ATS parsability, section completeness, proper structure
- content: action verbs, quantified results, strong descriptions
- skills: relevance, breadth, and depth of skills listed
- experience: years, progression, recency, relevance
- education: degree level, relevance, completeness

Be brutally honest. Identify ALL issues. Generate at minimum 5 feedbacks, maximum 15. Prioritize critical issues first.
PROMPT;
    }

    /**
     * Parse Claude's JSON response.
     * Handles cases where Claude may wrap JSON in markdown despite instructions.
     */
    private function parseAIResponse(array $apiResponse): array
    {
        $content = $apiResponse['content'][0]['text'] ?? '';

        // Strip markdown code fences if present
        $content = preg_replace('/```(?:json)?\s*/i', '', $content);
        $content = preg_replace('/```\s*$/', '', $content);
        $content = trim($content);

        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Failed to parse AI response', ['content' => substr($content, 0, 500)]);
            throw new \RuntimeException('AI returned invalid JSON: ' . json_last_error_msg());
        }

        return $data;
    }

    /** Save AI scores to analyses table */
    private function persistAnalysis(Resume $resume, array $data, float $duration, array $apiResponse): void
    {
        $scores  = $data['scores'] ?? [];
        $format  = $data['format'] ?? [];
        $content = $data['content'] ?? [];
        $exp     = $data['experience'] ?? [];
        $edu     = $data['education'] ?? [];
        $kw      = $data['keywords'] ?? [];
        $cand    = $data['candidate'] ?? [];

        // Update candidate info on the resume itself
        $resume->update([
            'candidate_name'     => $cand['name'] ?? null,
            'candidate_email'    => $cand['email'] ?? null,
            'candidate_phone'    => $cand['phone'] ?? null,
            'candidate_location' => $cand['location'] ?? null,
            'candidate_linkedin' => $cand['linkedin'] ?? null,
            'candidate_github'   => $cand['github'] ?? null,
        ]);

        Analysis::updateOrCreate(
            ['resume_id' => $resume->id],
            [
                'overall_score'            => $scores['overall'] ?? 0,
                'keyword_score'            => $scores['keyword'] ?? 0,
                'format_score'             => $scores['format'] ?? 0,
                'content_score'            => $scores['content'] ?? 0,
                'skills_score'             => $scores['skills'] ?? 0,
                'experience_score'         => $scores['experience'] ?? 0,
                'education_score'          => $scores['education'] ?? 0,
                'matched_keywords'         => $kw['matched'] ?? [],
                'missing_keywords'         => $kw['missing'] ?? [],
                'extra_keywords'           => $kw['extra'] ?? [],
                'keyword_density'          => $kw['density'] ?? 0,
                'has_contact_section'      => $format['has_contact'] ?? false,
                'has_summary_section'      => $format['has_summary'] ?? false,
                'has_experience_section'   => $format['has_experience'] ?? false,
                'has_education_section'    => $format['has_education'] ?? false,
                'has_skills_section'       => $format['has_skills'] ?? false,
                'has_projects_section'     => $format['has_projects'] ?? false,
                'has_certifications_section' => $format['has_certifications'] ?? false,
                'uses_tables'              => $format['uses_tables'] ?? false,
                'uses_graphics'            => $format['uses_graphics'] ?? false,
                'uses_columns'             => $format['uses_columns'] ?? false,
                'page_count'               => $format['page_count'] ?? null,
                'word_count'               => $format['word_count'] ?? null,
                'action_verbs_found'       => $content['action_verbs'] ?? [],
                'quantified_achievements'  => $content['quantified_achievements'] ?? 0,
                'has_dates'                => $content['has_dates'] ?? false,
                'has_metrics'              => $content['has_metrics'] ?? false,
                'total_experience_years'   => $exp['total_years'] ?? null,
                'job_count'                => $exp['job_count'] ?? null,
                'most_recent_title'        => $exp['most_recent_title'] ?? null,
                'most_recent_company'      => $exp['most_recent_company'] ?? null,
                'career_progression'       => $exp['career_progression'] ?? [],
                'highest_degree'           => $edu['highest_degree'] ?? null,
                'field_of_study'           => $edu['field_of_study'] ?? null,
                'institution'              => $edu['institution'] ?? null,
                'graduation_year'          => $edu['graduation_year'] ?? null,
                'ats_compatibility'        => $format['ats_compatibility'] ?? 'fair',
                'ats_warnings'             => $format['ats_warnings'] ?? [],
                'ai_model_used'            => self::MODEL,
                'ai_tokens_used'           => $apiResponse['usage']['output_tokens'] ?? null,
                'analysis_duration_seconds' => $duration,
            ]
        );
    }

    /** Delete old feedbacks and insert fresh ones */
    private function persistFeedbackItems(Resume $resume, array $feedbacks): void
    {
        $resume->feedback_items()->delete();

        foreach ($feedbacks as $fb) {
            FeedbackItem::create([
                'resume_id'   => $resume->id,
                'priority'    => $fb['priority'] ?? 'medium',
                'category'    => $fb['category'] ?? 'other',
                'title'       => substr($fb['title'] ?? 'Improvement needed', 0, 255),
                'description' => $fb['description'] ?? '',
                'suggestion'  => $fb['suggestion'] ?? '',
                'example'     => $fb['example'] ?? null,
            ]);
        }
    }

    /** Delete old skills and insert fresh extracted ones */
    private function persistSkills(Resume $resume, array $skills): void
    {
        $resume->skills()->delete();

        foreach ($skills as $skill) {
            Skill::create([
                'resume_id'             => $resume->id,
                'name'                  => substr($skill['name'] ?? '', 0, 100),
                'category'              => $skill['category'] ?? 'other',
                'proficiency'           => $skill['proficiency'] ?? 'unknown',
                'is_in_job_description' => (bool) ($skill['is_in_job_description'] ?? false),
                'mention_count'         => (int) ($skill['mention_count'] ?? 1),
            ]);
        }
    }

    /** Build keyword frequency map from top_words */
    private function persistKeywords(Resume $resume, array $keywordData): void
    {
        $resume->keywords()->delete();

        $topWords = $keywordData['top_words'] ?? [];
        $matched  = $keywordData['matched'] ?? [];

        foreach ($topWords as $kw) {
            Keyword::create([
                'resume_id'             => $resume->id,
                'word'                  => strtolower(substr($kw['word'] ?? '', 0, 100)),
                'frequency'             => (int) ($kw['frequency'] ?? 1),
                'is_ats_keyword'        => (bool) ($kw['is_ats_keyword'] ?? false),
                'is_in_job_description' => in_array(strtolower($kw['word'] ?? ''), array_map('strtolower', $matched)),
            ]);
        }
    }
}
