<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only authenticated users can create posts
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
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

    /**
     * Custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'A post title is required.',
            'title.min' => 'The title must be at least 5 characters long.',
            'title.max' => 'The title cannot exceed 255 characters.',
            'body.required' => 'Post content is required.',
            'body.min' => 'Your post content must be at least 100 characters long. Please add more details.',
            'slug.regex' => 'The slug may only contain lowercase letters, numbers, and hyphens.',
            'slug.unique' => 'This slug is already taken. Please choose a different one.',
            'category_ids.*.exists' => 'One or more selected categories are invalid.',
            'status.required' => 'Please select a status (Draft or Published).',
            'status.in' => 'The status must be either draft or published.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // If slug is empty, it will be generated from title in the controller
        if ($this->slug === null) {
            $this->merge([
                'slug' => null
            ]);
        }
    }
}
