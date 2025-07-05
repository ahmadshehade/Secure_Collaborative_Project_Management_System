<?php

namespace App\Http\Requests\Task;

use App\Http\Requests\BaseRequest;
use App\Models\Project;
use App\Models\Task;
use App\Rules\AssignedUserIsProjectMember;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth('api')->user();
        $taskId = $this->route('id');


        if ($user->hasRole('project_manager')) {
            return true;
        }


        return Task::where('id', $taskId)
            ->whereHas('project.members', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->exists();
    }


    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $taskId = $this->route('id');

        return [
            'assigned_to_user_id' => ['sometimes', 'integer', new AssignedUserIsProjectMember($this->input('project_id'))],
            'project_id'   => ['sometimes', 'exists:projects,id'],
            'name'         => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('tasks', 'name')->ignore($taskId),
            ],
            'description'  => ['nullable', 'string'],
            'status'       => ['required', 'in:pending,in_progress,completed'],
            'priority'     => ['nullable', 'in:low,medium,high'],
            'due_date'     => ['nullable', 'date_format:Y-m-d'],
            'attachment' => ['nullable', 'array'],
            'attachment.*' => [
                'file',
                'mimetypes:image/jpeg,image/png,image/webp,video/mp4,video/webm,video/ogg,application/pdf',
                'max:10240',
            ],
        ];
    }

    /**
     * Summary of messages
     * @return array{assigned_to_user_id.exists: string, assigned_to_user_id.integer: string, attachment.*.file: string, attachment.*.max: string, attachment.*.mimetypes: string, attachment.array: string, description.string: string, due_date.date_format: string, name.max: string, name.required: string, name.string: string, name.unique: string, priority.in: string, project_id.exists: string, status.in: string, status.required: string}
     */
    public function messages(): array
    {
        return [
            'assigned_to_user_id.integer' => 'assigned_to_user_id Must Be Integer',
           

            'project_id.exists'     => 'The selected project does not exist.',

            'name.required'         => 'The task name is required.',
            'name.string'           => 'The task name must be a string.',
            'name.max'              => 'The task name may not be greater than :max characters.',
            'name.unique'           => 'The task name must be unique.',

            'description.string'    => 'The description must be a string.',

            'status.required'       => 'The status is required.',
            'status.in'             => 'The selected status is invalid.',

            'priority.in'           => 'The selected priority is invalid.',

            'due_date.date_format'  => 'The due date must be in the format :format.',

            'attachment.array' => 'Attachments must be sent as an array of files.',
            'attachment.*.file' => 'Each attachment must be a valid file.',
            'attachment.*.mimetypes' => 'Each file must be an image (JPEG, PNG, WebP), a video (MP4, WebM, OGG), or a PDF document.',
            'attachment.*.max' => 'Each file must not exceed 10 MB.',
        ];
    }

    /**
     * Summary of attributes
     * @return array{assigned_to_user_id: string, attachment: string, description: string, due_date: string, name: string, priority: string, project_id: string, status: string}
     */
    public function attributes(): array
    {
        return [
            'assigned_to_user_id' => ' Assigned To User',
            'project_id'  => 'project',
            'name'        => 'task name',
            'description' => 'description',
            'status'      => 'status',
            'priority'    => 'priority',
            'due_date'    => 'due date',
            'attachment' => 'Attachment'
        ];
    }
}
