<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadResumeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null; 
    }

    public function rules(): array
    {
        return [
            'resume' => [
                'required',
                'file',
                'mimes:pdf,docx,doc,txt',
                'max:5120',
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
