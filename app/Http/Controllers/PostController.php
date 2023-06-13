<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\PostView;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function home(): View
    {
        $latestPosts = Post::query()
            ->where('active', true)
            ->whereDate('published_at', '<', Carbon::now())
            ->orderBy('published_at', 'desc')
            ->with('categories', 'user')
            ->limit(1)
            ->first();

        $popularPosts = Post::query()
            ->leftJoin('upvote_downvotes', 'posts.id', 'upvote_downvotes.post_id')
            ->select('posts.*', DB::raw('COUNT(upvote_downvotes.id) as upvote_count'))
            ->where(function ($query) {
                $query
                    ->whereNull('upvote_downvotes.is_upvote')
                    ->orWhere('upvote_downvotes.is_upvote', 1);
            })
            ->where('active', 1)
            ->whereDate('published_at', '<', Carbon::now())
            ->orderByDesc('upvote_count')
            ->groupBy('posts.id')
            ->limit(5)
            ->with('categories', 'user')
            ->get();

        $user = \request()->user();
        if ($user) {
            $leftJoin = "(SELECT cp.category_id, cp.post_id FROM upvote_downvotes JOIN category_post cp ON upvote_downvotes.post_id = cp.post_id WHERE upvote_downvotes.is_upvote = 1 and upvote_downvotes.user_id = ?) as t";
            $recommendedPosts = Post::query()
                ->leftJoin('category_post as cp', 'posts.id', 'cp.post_id')
                ->leftJoin(DB::raw($leftJoin), function ($join) {
                    $join->on('t.category_id', '=', 'cp.category_id')
                        ->on('t.post_id', '<>', 'cp.post_id');
                })->select('posts.*')
                ->where('posts.id', '<>', DB::raw('t.post_id'))
                ->setBindings([$user->id])
                ->with('categories', 'user')
                ->limit(3)
                ->get();
        } else {
            $recommendedPosts = Post::query()
                ->leftJoin('post_views', 'posts.id', 'post_views.post_id')
                ->select('posts.*', DB::raw('COUNT(post_views.id) as view_count'))
                ->where('active', 1)
                ->whereDate('published_at', '<', Carbon::now())
                ->orderByDesc('view_count')
                ->groupBy('posts.id')
                ->limit(3)
                ->with('categories', 'user')
                ->get();
        }
        return view('home', compact('latestPosts', 'popularPosts', 'recommendedPosts'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Post $post): View
    {
        if (!$post->active || $post->published_at > Carbon::now()) {
            throw new NotFoundHttpException();
        }
        $next = Post::query()->where('active', true)
            ->whereDate('published_at', '<=', Carbon::now())
            ->whereDate('published_at', '<', $post->published_at)
            ->orderBy('published_at', 'desc')
            ->limit(1)
            ->first();

        $prev = Post::query()->where('active', true)
            ->whereDate('published_at', '<=', Carbon::now())
            ->whereDate('published_at', '>', $post->published_at)
            ->orderBy('published_at', 'asc')
            ->limit(1)
            ->first();
        $user = $request->user();
        PostView::create([
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'post_id' => $post->id,
            'user_id' => $user?->id,
        ]);
        return view('post.view', compact('post', 'next', 'prev'));
    }

    public function byCategory(Category $category)
    {
        $posts = Post::query()
            ->join('category_post', 'posts.id', '=', 'category_post.post_id')
            ->where('category_post.category_id', $category->id)
            ->where('active', true)
            ->whereDate('published_at', '<=', Carbon::now())
            ->orderBy('published_at', 'desc')
            ->select('posts.id', 'title', 'slug', 'thumbnail', 'active', 'published_at', 'user_id', 'body')
            ->with('categories', 'user')
            ->paginate(10);
        return \view('post.index', compact('posts', 'category'));
    }
}
