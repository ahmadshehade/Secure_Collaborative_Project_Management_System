<?php

namespace App\Http\Requests\Comment;

use App\Http\Requests\BaseRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {

        return auth('api')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content' => 'required|string|max:2000',
            'commentable_type' => 'required|string|in:project,task',
            'commentable_id' => 'required|integer|exists:' . $this->resolveCommentableTable() . ',id',
            'attachment' => ['nullable', 'array'],
            'attachment.*' => [
                'file',
                'mimetypes:image/jpeg,image/png,image/webp',
                'max:10240',
            ],
        ];
    }
    /**
     * Summary of messages
     * @return array{attachment.*.file: string, attachment.*.max: string, attachment.*.mimetypes: string, attachment.array: string, commentable_id.exists: string, commentable_id.integer: string, commentable_id.required: string, commentable_type.in: string, commentable_type.required: string, content.max: string, content.required: string, content.string: string}
     */
    public function messages(): array
    {
        return [
            'content.required' => 'The comment content is required.',
            'content.string' => 'The comment content must be a valid string.',
            'content.max' => 'The comment content may not be greater than 2000 characters.',

            'commentable_type.required' => 'The commentable type is required.',
            'commentable_type.in' => 'The commentable type must be either project or task.',

            'commentable_id.required' => 'The commentable ID is required.',
            'commentable_id.integer' => 'The commentable ID must be a valid integer.',
            'commentable_id.exists' => 'The selected commentable item does not exist.',
            'attachment.array' => 'Attachments must be sent as an array of files.',
            'attachment.*.file' => 'Each attachment must be a valid file.',
            'attachment.*.mimetypes' => 'Each file must be an image (JPEG, PNG, WebP)',
            'attachment.*.max' => 'Each file must not exceed 10 MB.',
        ];
    }

    /**
     * Summary of attributes
     * @return array{attachment: string, commentable_id: string, commentable_type: string, content: string}
     */
    public function attributes(): array
    {
        return [
            'content' => 'comment content',
            'commentable_type' => 'commentable type',
            'commentable_id' => 'commentable ID',
            'attachment' => 'Attachment'
        ];
    }

    /**
     * Summary of resolveCommentableTable
     * @return string
     */
    protected function resolveCommentableTable()
    {
        return $this->input('commentable_type') === 'project' ? 'projects' : 'tasks';
    }

    /**
     * Summary of withValidator
     * @param mixed $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = $this->user();
            $type = $this->input('commentable_type');
            $id = $this->input('commentable_id');
            if (!$type || !$id || !$user) {
                return;
            }
            if (!in_array($type, ['project', 'task'])) {
                return;
            }
            if ($user->hasRole('admin') || $user->hasRole('project_manager')) {
                return;
            }
            $authorized = false;
            if ($type === 'project') {
                $authorized = Project::where('id', $id)
                    ->whereHas('members', fn($q) => $q->where('users.id', $user->id))
                    ->exists();
            }
            if ($type === 'task') {
                $authorized = Task::where('id', $id)
                    ->whereHas('project.members', fn($q) => $q->where('users.id', $user->id))
                    ->exists();
            }
            if (!$authorized) {
                $validator->errors()->add('commentable_id', 'You are not authorized to comment on this item.');
            }
        });
    }
}
