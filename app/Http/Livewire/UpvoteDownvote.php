<?php

namespace App\Http\Livewire;

use App\Models\Post;
use App\Models\User;
use Illuminate\View\View;
use Livewire\Component;

class UpvoteDownvote extends Component
{
    public Post $post;

    public function mount(Post $post): void
    {
        $this->post = $post;
    }

    public function render(): View
    {
        $upvotes = \App\Models\UpvoteDownvote::query()->where('post_id', $this->post->id)
            ->where('is_upvote', 1)->count();
        $downvotes = \App\Models\UpvoteDownvote::query()->where('post_id', $this->post->id)
            ->where('is_upvote', 0)->count();
        $hasUpvote = null;
        /** @var User $user */
        $user = request()->user();

        if ($user) {
            $model = \App\Models\UpvoteDownvote::query()
                ->where('post_id', $this->post->id)
                ->where('user_id', $user->id)->first();
            if ($model) {
                $hasUpvote = !!$model->is_upvote;
            }
        }

        return view('livewire.upvote-downvote', compact('upvotes', 'downvotes', 'hasUpvote'));
    }

    public function upvoteDownvote($upvote = true)
    {
        /** @var User $user */
        $user = request()->user();
        if (!$user) {
            $this->redirect(route('login'));
        }
        if (!$user->hasVerifiedEmail()) {
            $this->redirect(route('verification.notice'));
        }
        $model = \App\Models\UpvoteDownvote::query()
            ->where('post_id', $this->post->id)
            ->where('user_id', $user->id)->first();

        if (!$model) {
            \App\Models\UpvoteDownvote::create([
                'is_upvote' => $upvote,
                'post_id' => $this->post->id,
                'user_id' => $user->id,
            ]);
            return;
        }
        if ($upvote && $model->is_upvote || !$upvote && !$model->is_upvote) {
            $model->delete();
        } else {
            $model->is_upvote = $upvote;
            $model->save();
        }
    }
}
