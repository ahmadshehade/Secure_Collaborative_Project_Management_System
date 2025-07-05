<?php

namespace App\Http\Requests\Project;

use App\Http\Requests\BaseRequest;

class StoreProjectRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth('api')->user();

        if (!$user) return false;

        if ($user->hasRole('admin') || $user->hasRole('project_manager')) {
            return true;
        }

        $teamId = $this->input('team_id');

        if ($teamId) {
            return $user->ownedTeams()->where('id', $teamId)->exists();
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'team_id' => ['required', 'exists:teams,id'],
            'name' => ['required', 'string', 'max:255', 'unique:projects,name'],
            'description' => ['nullable', 'string'],
            'status' => ["sometimes", 'in:pending,in_progress,completed'],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
            'attachment' => ['nullable', 'array'],
            'attachment.*' => [
                'file',
                'mimetypes:image/jpeg,image/png,image/webp,video/mp4,video/webm,video/ogg,application/pdf',
                'max:10240',
            ],
        ];
    }

    /**
     * Get custom error messages for validator.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'team_id.required' => 'The team field is required.',
            'team_id.exists' => 'The selected team does not exist.',

            'name.required' => 'The project name is required.',
            'name.string' => 'The project name must be a string.',
            'name.max' => 'The project name must not exceed 255 characters.',
            'name.unique' => 'This project name is already in use.',


            'status.in' => 'The selected status is invalid.',

            'due_date.date' => 'The due date must be a valid date.',
            'due_date.after_or_equal' => 'The due date cannot be in the past.',

            'attachment.array' => 'Attachments must be sent as an array of files.',
            'attachment.*.file' => 'Each attachment must be a valid file.',
            'attachment.*.mimetypes' => 'Each file must be an image (JPEG, PNG, WebP), a video (MP4, WebM, OGG), or a PDF document.',
            'attachment.*.max' => 'Each file must not exceed 10 MB.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'team_id' => 'team',
            'name' => 'project name',
            'description' => 'project description',
            'status' => 'project status',
            'due_date' => 'due date',
            'attachment' => 'Attachment'
        ];
    }
}
