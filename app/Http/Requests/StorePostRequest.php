<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'slug' => ['nullable', 'string', 'unique:posts,slug', 'regex:/^[a-z0-9-]+$/'],
            'body' => ['required', 'string', 'min:100'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['exists:categories,id'],
            'status' => ['required', 'in:draft,published'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'A post title is required.',
            'title.min' => 'The title must be at least 5 characters long.',
            'body.required' => 'Post content is required.',
            'body.min' => 'Your post content must be at least 100 characters long.',
            'status.required' => 'Please select a status (Draft or Published).',
        ];
    }

    /**
     * Handle validation errors - redirect back to form for web requests
     */
    protected function failedValidation(Validator $validator)
    {
        // For API requests
        if ($this->expectsJson()) {
            throw new ValidationException($validator);
        }

        // For web requests - redirect back with errors
        throw (new ValidationException($validator))
            ->redirectTo($this->getRedirectUrl())
            ->errorBag($this->errorBag);
    }
}
