<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UploadResumeRequest
 *
 * Validates incoming resume upload.
 * Runs BEFORE the controller — keeps controller thin.
 *
 * Rules:
 * - resume: required, must be file, allowed mimes, max 5MB
 * - job_title: optional string for keyword matching context
 * - job_description: optional text pasted from job posting
 */
class UploadResumeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null; // Must be logged in
    }

    public function rules(): array
    {
        return [
            'resume' => [
                'required',
                'file',
                'mimes:pdf,docx,doc,txt',  // Allowed file types
                'max:5120',                 // 5MB limit in kilobytes
            ],
            'job_title'       => 'nullable|string|max:200',
            'job_description' => 'nullable|string|max:5000',
        ];
    }

    public function messages(): array
    {
        return [
            'resume.required' => 'Please select a resume file to upload.',
            'resume.mimes'    => 'Only PDF, DOCX, DOC, and TXT files are accepted.',
            'resume.max'      => 'Resume file must be under 5MB.',
        ];
    }
}
