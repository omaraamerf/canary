<?php

namespace App\Services;

use App\Enums\PostStatus;
use App\Exceptions\CloudinaryUploadException;
use App\Models\Comment;
use App\Models\Media;
use App\Models\Post;
use App\Models\User;
use App\Support\UniqueSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class CommunityService
{
    public function __construct(
        private readonly UniqueSlug $slugs,
        private readonly CloudinaryMediaService $cloudinary,
    ) {}

    public function createPost(User $author, array $data): Post
    {
        return $this->withUploadedMedia($data, fn (array $uploaded): Post => DB::transaction(function () use ($author, $data, $uploaded) {
            $post = $author->posts()->create([
                ...Arr::except($data, ['images', 'videos']),
                'slug' => $this->slugs->for(Post::class, $data['title'], 'post', null, true),
                'status' => PostStatus::Published->value,
            ]);

            $this->persistMedia($post, $uploaded);

            return $post;
        }));
    }

    public function addComment(Post $post, User $author, array $data): Comment
    {
        abort_unless($post->status === PostStatus::Published, 404);

        return $this->withUploadedMedia($data, fn (array $uploaded): Comment => DB::transaction(function () use ($post, $author, $data, $uploaded) {
            $comment = $post->comments()->create([
                'user_id' => $author->id,
                'body' => $data['body'],
            ]);

            $this->persistMedia($comment, $uploaded);

            return $comment;
        }));
    }

    public function toggleAcceptedComment(Post $post, Comment $comment): void
    {
        abort_unless($comment->commentable_type === $post->getMorphClass() && $comment->commentable_id === $post->id, 404);
        abort_if($comment->is_hidden, 422);

        $post->update([
            'accepted_comment_id' => $post->accepted_comment_id === $comment->id ? null : $comment->id,
        ]);
    }

    public function setPostStatus(Post $post, PostStatus $status): void
    {
        $post->update(['status' => $status->value]);
    }

    public function setCommentHidden(Comment $comment, bool $hidden): void
    {
        DB::transaction(function () use ($comment, $hidden) {
            $comment->update(['is_hidden' => $hidden]);

            if ($hidden) {
                Post::query()->where('accepted_comment_id', $comment->id)->update(['accepted_comment_id' => null]);
            }
        });
    }

    public function deletePost(Post $post): bool
    {
        return (bool) $post->delete();
    }

    public function forceDeletePost(Post $post): bool
    {
        $comments = $post->comments()->with('media')->get();
        $media = $post->media()->get()->concat($comments->pluck('media')->flatten(1));

        DB::transaction(function () use ($post, $media) {
            Media::query()->whereKey($media->pluck('id')->all())->delete();
            $post->comments()->delete();
            $post->forceDelete();
        });

        $this->cloudinary->deleteUploaded($media);

        return true;
    }

    public function deleteComment(Comment $comment): bool
    {
        $media = $comment->media()->get();

        DB::transaction(function () use ($comment) {
            Post::withTrashed()->where('accepted_comment_id', $comment->id)->update(['accepted_comment_id' => null]);
            $comment->media()->delete();
            $comment->delete();
        });

        $this->cloudinary->deleteUploaded($media);

        return true;
    }

    /**
     * @template T
     *
     * @param  callable(array): T  $persist
     * @return T
     */
    private function withUploadedMedia(array $data, callable $persist): mixed
    {
        try {
            $uploaded = $this->cloudinary->upload($data['images'] ?? [], $data['videos'] ?? []);
        } catch (CloudinaryUploadException $exception) {
            throw ValidationException::withMessages([$exception->field => $exception->getMessage()]);
        }

        try {
            return $persist($uploaded);
        } catch (Throwable $exception) {
            $this->cloudinary->deleteUploaded($uploaded);

            throw $exception;
        }
    }

    private function persistMedia(Model $owner, array $uploaded): void
    {
        foreach (array_values($uploaded) as $position => $media) {
            $owner->media()->create([...$media, 'sort_order' => $position + 1]);
        }
    }
}
