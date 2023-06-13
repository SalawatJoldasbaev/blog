<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\PostView;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $posts = Post::query()
            ->where('active', true)
            ->whereDate('published_at', '<', Carbon::now())
            ->orderBy('published_at', 'desc')
            ->select('posts.id', 'title', 'slug', 'thumbnail', 'active', 'published_at', 'user_id', 'body')
            ->with('categories', 'user')
            ->paginate(5);
        return view('home', compact('posts'));
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
            'post_id'=> $post->id,
            'user_id'=> $user?->id,
        ]);
        return \view('post.view', compact('post', 'next', 'prev'));
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
