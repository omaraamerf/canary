<?php

namespace App\Models;

use App\Enums\PostCategory;
use App\Enums\PostStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'breed_id', 'region_id', 'category', 'title', 'slug', 'body', 'status', 'accepted_comment_id',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'accepted_comment_id' => 'integer',
            'category' => PostCategory::class,
            'status' => PostStatus::class,
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breed()
    {
        return $this->belongsTo(Breed::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function visibleComments()
    {
        return $this->comments()->where('is_hidden', false);
    }

    public function acceptedComment()
    {
        return $this->belongsTo(Comment::class, 'accepted_comment_id');
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }

    public function scopePublished($query)
    {
        return $query->where('status', PostStatus::Published->value);
    }

    public function isSolved(): bool
    {
        return $this->accepted_comment_id !== null;
    }

    public function isOwnedBy(?User $user): bool
    {
        return $user !== null && $this->user_id === $user->id;
    }
}
