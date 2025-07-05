<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {

        $user = auth('api')->user();
        return  $user->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'role' => ['required', 'in:admin,member,project_manager'],
        ];
    }

    /**
     * Summary of messages
     * @return array{role.in: string, role.required: string}
     */
    public function messages(): array
    {
        return [
            'role.required' => 'Please select a role to assign to the user.',
            'role.in' => 'The selected role is invalid. Allowed roles are: admin, member, or project_manager.',
        ];
    }

    /**
     * Summary of attributes
     * @return array{role: string}
     */
    public function attributes(): array
    {
        return [
            'role' => 'role',
        ];
    }
}
