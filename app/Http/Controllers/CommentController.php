<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use App\Services\CommunityService;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function __construct(private readonly CommunityService $community) {}

    public function store(StoreCommentRequest $request, Post $post)
    {
        abort_unless($request->user()->can('view', $post), 404);
        Gate::authorize('comment', $post);

        $comment = $this->community->addComment($post, $request->user(), $request->validated());

        return redirect()->to(route('community.show', $post).'#comment-'.$comment->id);
    }

    public function destroy(Comment $comment)
    {
        Gate::authorize('delete', $comment);

        $post = $comment->commentable;
        $this->community->deleteComment($comment);

        return $post instanceof Post
            ? redirect()->to(route('community.show', $post).'#comments')
            : redirect()->route('community.index');
    }
}
