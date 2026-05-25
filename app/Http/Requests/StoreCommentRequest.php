<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only authenticated users can comment
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'min:3', 'max:1000'],
            'post_id' => ['required', 'exists:posts,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => 'Comment content is required.',
            'body.min' => 'Your comment must be at least 3 characters long.',
            'body.max' => 'Your comment cannot exceed 1000 characters.',
            'post_id.required' => 'Unable to identify which post to comment on.',
            'post_id.exists' => 'The post you are trying to comment on does not exist.',
        ];
    }
}
