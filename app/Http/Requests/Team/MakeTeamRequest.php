<?php

namespace App\Http\Requests\Team;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MakeTeamRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth('api')->user();
        if ($user->hasRole("admin") || $user->hasRole("member")) {
            return true;
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
        'name' => ['required', 'string', 'max:200', 'unique:teams,name'],
        'members' => ['required', 'array'],
        'members.*' => [
            'integer',
            Rule::exists('users', 'id')->where(function ($query) {
                $query->where('role', 'member');
            }),
        ],
    ];
}
    /**
     * Summary of messages
     * @return array{members.*.exists: string, members.array: string, name.max: string, name.required: string, name.unique: string}
     */
    public function messages(): array
    {
        return [
       'name.required' => 'The team name is required.',
        'name.string' => 'The team name must be a string.',
        'name.max' => 'The team name must not exceed 200 characters.',
        'name.unique' => 'The team name has already been taken.',
         'members.required'=>'The  Member Must Be Required',
        'members.array' => 'Members must be provided as an array.',
        'members.*.exists' => 'Each selected member must be a valid user with the "member" role.',
        ];
    }

    /**
     * Summary of attributes
     * @return array{members: string, members.*: string, name: string}
     */
    public function attributes(): array
    {
        return [
            'name' => 'team name',
            'members' => 'team members',
            'members.*' => 'member',
        ];
    }
}
