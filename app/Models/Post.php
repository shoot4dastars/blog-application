<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'body',
        'user_id',
        'view_count'
    ];

    protected $casts = [
        'view_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function status()
    {
        return $this->morphOne(Status::class, 'statusable');
    }

    public function scopePublished($query)
    {
        return $query->whereHas('status', function ($q) {
            $q->where('status', 'published');
        });
    }

    public function isPublished()
    {
        return $this->status && $this->status->status === 'published';
    }
}
