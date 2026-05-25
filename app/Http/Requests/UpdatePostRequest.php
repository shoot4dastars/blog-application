<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $post = $this->route('post');

        // Only the post owner or admin can update
        return auth()->check() && (
                auth()->id() === $post->user_id ||
                auth()->user()->isAdmin()
            );
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $post = $this->route('post');

        return [
            'title' => ['sometimes', 'string', 'min:5', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('posts', 'slug')->ignore($post->id),
            ],
            'body' => ['sometimes', 'string', 'min:100'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['exists:categories,id'],
            'status' => ['sometimes', 'in:draft,published'],
        ];
    }

    /**
     * Custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'title.min' => 'The title must be at least 5 characters long.',
            'title.max' => 'The title cannot exceed 255 characters.',
            'body.min' => 'Your post content must be at least 100 characters long.',
            'slug.regex' => 'The slug may only contain lowercase letters, numbers, and hyphens.',
            'slug.unique' => 'This slug is already taken. Please choose a different one.',
            'category_ids.*.exists' => 'One or more selected categories are invalid.',
            'status.in' => 'The status must be either draft or published.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => 'post title',
            'body' => 'post content',
            'slug' => 'URL slug',
            'category_ids' => 'categories',
            'status' => 'post status',
        ];
    }
}
