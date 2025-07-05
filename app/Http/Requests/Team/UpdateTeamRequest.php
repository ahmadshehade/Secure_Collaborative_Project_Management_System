<?php

namespace App\Http\Requests\Team;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeamRequest extends BaseRequest
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
        $teamId = $this->route("id");
        return [
            'name' => ['sometimes', 'string', 'max:200', Rule::unique('teams')->ignore($teamId)],
             'members' => ['sometimes', 'array'],
            'members.*' => [
            'integer',
            Rule::exists('users', 'id')->where(function ($query) {
                $query->where('role', 'member');
            }),
        ],
        ];
    }


    public function messages()
    {
        return [
            'name.required' => 'Please enter the team name.',
            'name.max' => 'The team name must not exceed 200 characters.',
             'members.array' => 'Members must be provided as an array.',
             'members.*.exists' => 'Each selected member must be a valid user with the "member" role.',
        ];
    }




    public function attributes()
    {
        return [
            'name' => 'Team Name',
            'members' => 'Team Members',
            'members.*' => 'Member',
        ];
    }
}
