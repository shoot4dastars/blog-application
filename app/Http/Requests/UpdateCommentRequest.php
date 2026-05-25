<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $comment = $this->route('comment');

        // Only comment owner or admin can edit
        return auth()->check() && (
                auth()->id() === $comment->user_id ||
                auth()->user()->isAdmin()
            );
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'min:3', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => 'Comment content is required.',
            'body.min' => 'Your comment must be at least 3 characters long.',
            'body.max' => 'Your comment cannot exceed 1000 characters.',
        ];
    }
}
