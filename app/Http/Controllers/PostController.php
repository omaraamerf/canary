<?php

namespace App\Http\Controllers;

use App\Enums\PostCategory;
use App\Http\Requests\StorePostRequest;
use App\Models\Breed;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Region;
use App\Services\CommunityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    public function __construct(private readonly CommunityService $community) {}

    public function index(Request $request)
    {
        $posts = Post::query()
            ->published()
            ->with(['user.sellerProfile', 'breed', 'media'])
            ->withCount('visibleComments')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q')->trim().'%';
                $query->where(fn ($nested) => $nested->where('title', 'like', $term)->orWhere('body', 'like', $term));
            })
            ->when(PostCategory::tryFrom($request->string('category')->toString()), fn ($query, $category) => $query->where('category', $category->value))
            ->when($request->filled('breed'), fn ($query) => $query->whereHas('breed', fn ($breed) => $breed->where('slug', $request->string('breed'))))
            ->when($request->string('state')->toString() === 'solved', fn ($query) => $query->whereNotNull('accepted_comment_id'))
            ->when($request->string('state')->toString() === 'open', fn ($query) => $query->whereNull('accepted_comment_id'))
            ->latest();

        return view('community.index', [
            'posts' => $posts->paginate(12)->withQueryString(),
            'breeds' => Breed::where('active', true)->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('community.create', [
            'breeds' => Breed::where('active', true)->orderBy('name')->get(),
            'regions' => Region::where('active', true)->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function store(StorePostRequest $request)
    {
        Gate::authorize('create', Post::class);

        $post = $this->community->createPost($request->user(), $request->validated());

        return redirect()->route('community.show', $post)->with('success', __('ui.community.published'));
    }

    public function show(Request $request, Post $post)
    {
        abort_unless(Gate::forUser($request->user())->allows('view', $post), 404);

        $post->load(['user.sellerProfile', 'breed', 'region', 'media']);
        $comments = $post->visibleComments()
            ->with(['user.sellerProfile', 'media'])
            ->oldest()
            ->get()
            ->sortByDesc(fn (Comment $comment): bool => $comment->id === $post->accepted_comment_id)
            ->values();

        return view('community.show', compact('post', 'comments'));
    }

    public function destroy(Post $post)
    {
        Gate::authorize('delete', $post);

        $this->community->deletePost($post);

        return redirect()->route('community.index')->with('success', __('ui.community.deleted'));
    }

    public function accept(Post $post, Comment $comment)
    {
        Gate::authorize('acceptComment', $post);

        $this->community->toggleAcceptedComment($post, $comment);

        return redirect()->to(route('community.show', $post).'#comment-'.$comment->id);
    }
}
