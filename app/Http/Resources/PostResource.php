<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'body' => $this->body,
            'excerpt' => \Str::limit($this->body, 150),
            'status' => $this->status ? $this->status->status : null,
            'view_count' => $this->view_count,
            'author' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'categories' => $this->whenLoaded('categories', function () {
                return $this->categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                    ];
                });
            }),
            'comments_count' => $this->whenLoaded('comments', function () {
                return $this->comments->count();
            }),
            'comments' => $this->whenLoaded('comments', function () {
                return CommentResource::collection($this->comments);
            }),
            'created_at' => $this->created_at->toISO8601String(),
            'updated_at' => $this->updated_at->toISO8601String(),
        ];
    }
}
