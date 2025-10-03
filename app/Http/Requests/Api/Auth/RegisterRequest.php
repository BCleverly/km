<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\BaseApiRequest;
use App\TargetUserType;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'user_type' => ['required', 'integer', 'in:' . implode(',', array_column(TargetUserType::cases(), 'value'))],
            'username' => ['nullable', 'string', 'max:255', 'unique:profiles,username'],
            'bdsm_role' => ['nullable', 'integer', 'in:1,2,3'], // Dominant, Submissive, Switch
            'about' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'user_type.in' => 'The user type must be Male (1), Female (2), or Couple (3).',
            'bdsm_role.in' => 'The BDSM role must be Dominant (1), Submissive (2), or Switch (3).',
            'username.unique' => 'This username is already taken.',
        ]);
    }
}