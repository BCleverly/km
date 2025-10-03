<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

abstract class BaseApiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        $response = response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422);

        throw new HttpResponseException($response);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    abstract public function rules(): array;

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'required' => 'The :attribute field is required.',
            'email' => 'The :attribute must be a valid email address.',
            'unique' => 'The :attribute has already been taken.',
            'confirmed' => 'The :attribute confirmation does not match.',
            'min' => 'The :attribute must be at least :min characters.',
            'max' => 'The :attribute may not be greater than :max characters.',
            'integer' => 'The :attribute must be an integer.',
            'boolean' => 'The :attribute must be true or false.',
            'array' => 'The :attribute must be an array.',
            'string' => 'The :attribute must be a string.',
            'image' => 'The :attribute must be an image.',
            'in' => 'The selected :attribute is invalid.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'email' => 'email address',
            'password' => 'password',
            'name' => 'name',
            'username' => 'username',
            'user_type' => 'user type',
            'bdsm_role' => 'BDSM role',
            'about' => 'about',
            'completion_note' => 'completion note',
            'completion_image' => 'completion image',
            'content' => 'content',
            'title' => 'title',
            'summary' => 'summary',
            'is_public' => 'public status',
            'is_premium' => 'premium status',
            'is_anonymous' => 'anonymous status',
            'tags' => 'tags',
            'plan' => 'subscription plan',
            'reactable_type' => 'content type',
            'reactable_id' => 'content ID',
            'reaction_type' => 'reaction type',
        ];
    }
}