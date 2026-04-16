<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Resume;
use App\Models\Analysis;
use App\Models\FeedbackItem as Feedback;
use App\Models\Skill;
use App\Models\Keyword;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Demo User ──────────────────────────────────────────
        $user = User::updateOrCreate(
            ['email' => 'prakash@gmail.com'],
            [
                'name'         => 'Prakash',
                'password'     => Hash::make('Prakash@123'),
                'plan'         => 'pro',
                'upload_count' => 3,
            ]
        );

        $this->command->info("✅ Demo user: prakash@gmail.com / Prakash@123");

        $resume1 = Resume::create([
            'user_id'           => $user->id,
            'original_filename' => 'prakash_resume.pdf',
            'stored_filename'   => 'seed-resume-1.pdf',
            'file_path'         => 'resumes/seed/seed-resume-1.pdf',
            'file_type'         => 'pdf',
            'file_size'         => 187432,
            'raw_text'          => 'Prakash | Software Engineer | prakash@gmail.com | Bangalore, India...',
            'candidate_name'    => 'Prakash',
            'candidate_email'   => 'prakash@gmail.com',
            'candidate_location'=> 'Kathmandu, Nepal',
            'candidate_linkedin'=> 'linkedin.com/in/prakash',
            'candidate_github'  => 'github.com/prakash',
            'target_job_title'  => 'Senior Software Engineer',
            'status'            => 'analyzed',
            'analyzed_at'       => now()->subDays(2),
        ]);

        Analysis::create([
            'resume_id'                 => $resume1->id,
            'overall_score'             => 78,
            'keyword_score'             => 82,
            'format_score'              => 85,
            'content_score'             => 74,
            'skills_score'              => 80,
            'experience_score'          => 76,
            'education_score'           => 70,
            'matched_keywords'          => ['Python', 'Django', 'AWS', 'Docker', 'PostgreSQL', 'REST API', 'Git'],
            'missing_keywords'          => ['Kubernetes', 'Terraform', 'CI/CD', 'Microservices'],
            'extra_keywords'            => ['Flask', 'Redis', 'Celery'],
            'keyword_density'           => 4.2,
            'has_contact_section'       => true,
            'has_summary_section'       => true,
            'has_experience_section'    => true,
            'has_education_section'     => true,
            'has_skills_section'        => true,
            'has_projects_section'      => true,
            'has_certifications_section'=> false,
            'uses_tables'               => false,
            'uses_graphics'             => false,
            'uses_columns'              => false,
            'page_count'                => 2,
            'word_count'                => 620,
            'action_verbs_found'        => ['Led', 'Built', 'Deployed', 'Architected', 'Optimized', 'Reduced'],
            'quantified_achievements'   => 5,
            'has_dates'                 => true,
            'has_metrics'               => true,
            'total_experience_years'    => 6,
            'job_count'                 => 3,
            'most_recent_title'         => 'Senior Software Engineer',
            'most_recent_company'       => 'TechCorp Inc.',
            'career_progression'        => [
                ['title' => 'Junior Developer', 'company' => 'StartupXYZ', 'start' => '2018-06', 'end' => '2020-08', 'duration_months' => 26],
                ['title' => 'Software Engineer', 'company' => 'MidSizeCo', 'start' => '2020-09', 'end' => '2022-11', 'duration_months' => 26],
                ['title' => 'Senior Software Engineer', 'company' => 'TechCorp Inc.', 'start' => '2022-12', 'end' => 'Present', 'duration_months' => 16],
            ],
            'highest_degree'            => "Bachelor's",
            'field_of_study'            => 'Computer Science',
            'institution'               => 'UC Berkeley',
            'graduation_year'           => 2018,
            'ats_compatibility'         => 'good',
            'ats_warnings'              => ['Two-column layout may cause parsing issues in some ATS systems'],
            'ai_model_used'             => 'claude-sonnet-4-20250514',
            'ai_tokens_used'            => 1842,
            'analysis_duration_seconds' => 8.4,
        ]);

        $feedbacks1 = [
            ['critical', 'keywords', 'Add Kubernetes to Skills Section', 'Kubernetes appears 4 times in the job description but is completely absent from your resume. This is a critical ATS miss.', 'Add "Kubernetes" to your technical skills section and mention it in at least one job bullet where you worked with containerized applications.', 'Example: "Orchestrated microservices deployment using Kubernetes (K8s) across 3 production clusters"'],
            ['high', 'content', 'Quantify More Achievements', 'Only 5 of your 18 bullet points include numbers or metrics. ATS scores favor measurable impact statements.', 'Add specific numbers (%, $, users, ms, GB) to at least 60% of your bullet points.', 'Before: "Improved API performance"\nAfter: "Reduced API response time by 340ms (67%) through Redis caching implementation"'],
            ['high', 'keywords', 'Missing CI/CD Terminology', 'CI/CD appears in 80% of senior engineering job descriptions but is absent from your resume.', 'Add CI/CD pipeline experience to relevant experience bullets. Mention specific tools: Jenkins, GitHub Actions, GitLab CI.', null],
            ['medium', 'summary', 'Strengthen Professional Summary', 'Your current summary is generic. It does not mention your specialization or career impact.', 'Rewrite with: seniority + specialization + 1 major achievement + target role.', 'Example: "Senior Software Engineer with 6+ years building scalable Python/Django APIs serving 2M+ users. Led migration to microservices that cut infrastructure costs 40%. Seeking senior backend roles in high-growth fintech."'],
            ['low', 'education', 'Add GPA if Above 3.5', 'Your UC Berkeley CS degree is impressive. If your GPA was 3.5 or higher, adding it strengthens your profile for technical roles.', 'Add "GPA: X.X/4.0 (if ≥ 3.5)" next to your degree line.', null],
        ];

        foreach ($feedbacks1 as [$priority, $category, $title, $desc, $suggestion, $example]) {
            Feedback::create([
                'resume_id'   => $resume1->id,
                'priority'    => $priority,
                'category'    => $category,
                'title'       => $title,
                'description' => $desc,
                'suggestion'  => $suggestion,
                'example'     => $example,
            ]);
        }

        $skills1 = [
            ['Python', 'technical', 'expert', true, 8],
            ['Django', 'framework', 'advanced', true, 5],
            ['AWS', 'tool', 'advanced', true, 4],
            ['Docker', 'tool', 'intermediate', true, 3],
            ['PostgreSQL', 'technical', 'advanced', true, 4],
            ['Redis', 'tool', 'intermediate', false, 2],
            ['React', 'framework', 'intermediate', false, 2],
            ['Git', 'tool', 'expert', true, 3],
            ['Leadership', 'soft', 'advanced', false, 2],
        ];

        foreach ($skills1 as [$name, $cat, $prof, $inJD, $count]) {
            Skill::create([
                'resume_id' => $resume1->id, 'name' => $name, 'category' => $cat,
                'proficiency' => $prof, 'is_in_job_description' => $inJD, 'mention_count' => $count,
            ]);
        }

        // ── Resume 2: Marketing Manager (Fair score) ───────────
        $resume2 = Resume::create([
            'user_id'           => $user->id,
            'original_filename' => 'Alex_Johnson_Marketing.pdf',
            'stored_filename'   => 'seed-resume-2.pdf',
            'file_path'         => 'resumes/seed/seed-resume-2.pdf',
            'file_type'         => 'pdf',
            'file_size'         => 142210,
            'candidate_name'    => 'Alex Johnson',
            'candidate_email'   => 'alex@example.com',
            'target_job_title'  => 'Digital Marketing Manager',
            'status'            => 'analyzed',
            'analyzed_at'       => \Carbon\Carbon::now()->subDays(5),
        ]);

        Analysis::create([
            'resume_id'              => $resume2->id,
            'overall_score'          => 54,
            'keyword_score'          => 48,
            'format_score'           => 72,
            'content_score'          => 50,
            'skills_score'           => 60,
            'experience_score'       => 55,
            'education_score'        => 65,
            'matched_keywords'       => ['SEO', 'Google Analytics', 'Content Marketing'],
            'missing_keywords'       => ['HubSpot', 'Salesforce', 'A/B Testing', 'Email Marketing', 'PPC', 'CRM'],
            'extra_keywords'         => ['Photoshop', 'Canva'],
            'keyword_density'        => 2.8,
            'has_contact_section'    => true,
            'has_summary_section'    => false,
            'has_experience_section' => true,
            'has_education_section'  => true,
            'has_skills_section'     => true,
            'has_projects_section'   => false,
            'has_certifications_section' => false,
            'uses_tables'            => true,
            'uses_graphics'          => false,
            'uses_columns'           => false,
            'page_count'             => 1,
            'word_count'             => 380,
            'action_verbs_found'     => ['Managed', 'Created', 'Worked'],
            'quantified_achievements'=> 1,
            'has_dates'              => true,
            'has_metrics'            => false,
            'total_experience_years' => 4,
            'job_count'              => 2,
            'most_recent_title'      => 'Marketing Coordinator',
            'most_recent_company'    => 'BrandCo',
            'career_progression'     => [],
            'highest_degree'         => "Bachelor's",
            'field_of_study'         => 'Marketing',
            'institution'            => 'State University',
            'graduation_year'        => 2020,
            'ats_compatibility'      => 'fair',
            'ats_warnings'           => [
                'Table detected — most ATS systems cannot parse table content',
                'Too short — one page may not demonstrate sufficient experience for managerial role',
                'No professional summary — ATS keyword-heavy summaries significantly boost scores',
            ],
            'ai_model_used'          => 'claude-sonnet-4-20250514',
            'ai_tokens_used'         => 1654,
            'analysis_duration_seconds' => 7.1,
        ]);

        $feedbacks2 = [
            ['critical', 'ats', 'Remove Table — ATS Cannot Parse It', 'Your skills section uses an HTML/Word table. 75% of ATS systems skip table content entirely, making your skills invisible.', 'Delete the table and list skills as plain comma-separated text or a simple bullet list.', "Before (table cell): | Python | JavaScript |\nAfter (plain text): Python, JavaScript, SQL, Google Analytics, SEO"],
            ['critical', 'summary', 'Add Professional Summary Section', 'Missing summary is your biggest ATS weakness. The first 3 lines of a resume carry the most keyword weight in ATS scoring.', 'Add a 3-4 line summary directly below your contact info with your title, years of experience, specialization, and 2-3 key marketing skills from the job description.', "Digital Marketing Manager with 4+ years driving growth through data-driven campaigns. Expertise in SEO, HubSpot CRM, and multi-channel content strategy. Delivered 45% organic traffic increase for B2B SaaS clients."],
            ['high', 'keywords', 'Add HubSpot and CRM Experience', 'HubSpot appears 6 times in the target JD. It is the most sought-after tool for this role and is entirely absent from your resume.', 'If you have used HubSpot or any CRM (Salesforce, Zoho, Pipedrive), add it immediately to skills and mention in relevant experience bullets.', null],
            ['high', 'achievements', 'All Bullet Points Need Metrics', 'Only 1 of 12 bullet points has a number. Marketing managers are expected to show ROI. No metrics = no proof of impact.', 'Add % growth, $ revenue, follower counts, conversion rates, ROAS, or CTR to every achievement.', "Before: Managed social media accounts\nAfter: Grew Instagram following from 2.4K to 18K in 8 months through organic content strategy (650% growth)"],
            ['medium', 'length', 'Expand to 1.5–2 Pages for Managerial Role', 'At 380 words, your resume is too sparse for a manager-level position. Decision makers need more evidence of scope and leadership.', 'Add: project descriptions, team size managed, budget ownership, vendor relationships, or campaign case studies.', null],
            ['medium', 'content', 'Replace Weak Action Verbs', '"Worked on" and "Helped with" are passive and generic. ATS and hiring managers discount vague language.', 'Replace with strong marketing verbs: Orchestrated, Spearheaded, Launched, Optimized, Scaled, Drove, Analyzed, Executed.', null],
        ];

        foreach ($feedbacks2 as [$priority, $category, $title, $desc, $suggestion, $example]) {
            Feedback::create([
                'resume_id'   => $resume2->id,
                'priority'    => $priority,
                'category'    => $category,
                'title'       => $title,
                'description' => $desc,
                'suggestion'  => $suggestion,
                'example'     => $example,
            ]);
        }

        // ── Resume 3: Recent upload (still processing) ─────────
        Resume::create([
            'user_id'           => $user->id,
            'original_filename' => 'Alex_Johnson_ProductManager.docx',
            'stored_filename'   => 'seed-resume-3.docx',
            'file_path'         => 'resumes/seed/seed-resume-3.docx',
            'file_type'         => 'docx',
            'file_size'         => 98304,
            'target_job_title'  => 'Senior Product Manager',
            'status'            => 'uploaded',
        ]);

        $this->command->info("✅ Seeded 3 resumes (2 analyzed, 1 pending)");
        $this->command->info("🚀 Login at /  with: prakash@gmail.com / Prakash@123");
    }
}
