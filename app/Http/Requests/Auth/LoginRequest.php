<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

class LoginRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "email" => ['required', 'email', 'exists:users,email'],
            "password" => ['required', 'min:8', 'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'],
        ];
    }

    /**
     * Summary of messages
     * @return array{email.email: string, email.exists: string, email.required: string, password.min: string, password.regex: string, password.required: string}
     */
    public  function  messages()
    {
        return [

            'email.email' => 'Please provide a valid email address.',
            'email.required' => 'The email field is required.',
            'email.exists' => 'This email is not in users table.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.regex' => 'The password must contain at least one uppercase letter, one number, and one special character.',

        ];
    }


    /**
     * Summary of attributes
     * @return array{email: string, name: string, password: string}
     */
    public  function attributes(): array
    {
        return [
            'name' => 'Name',
            'email' => 'Email address',
            'password' => 'Password',

        ];
    }
}
