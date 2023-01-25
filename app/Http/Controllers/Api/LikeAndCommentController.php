<?php

namespace App\Http\Controllers\Api;

use App\Events\NotiEvent;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Notification;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LikeAndCommentController extends Controller
{
    public function like_unlike($id, Request $request)
    {
        $like = Like::where('user_id', $request->user()->id)->where('post_id', $id)->first();
        if ($like) {
            Notification::where('value', 'like_id-' . $like->id)->delete();
            $like->delete();
            return response()->json(['status' => 'unlike'], 200);
        } else {
            $like = Like::create([
                'user_id' => $request->user()->id,
                'post_id' => $id,
            ]);
            if (Auth::user()->id != $like->post->user_id) {
                $noti = Notification::create([
                    'user_id' => $like->post->user_id,
                    'value' => 'like_id-' . $like->id,
                    'description' => '<a href="/profile/view/' . Auth::user()->id . '" >' . Auth::user()->name . '</a>' . ' liked on your <a href="/posts/' . $like->post->id . '">' . $like->post->name . '</a>',
                ]);
                $data = (object) [
                    'user_id' => $noti->user_id,
                    'body' => Auth::user()->name . ' like to your post ' . $like->post->name . '.',
                ];
                event(new NotiEvent(json_encode($data)));
            }
            return response()->json(['status' => 'like'], 200);
        }
    }

    public function like_count()
    {
        $like_count = Like::where('user_id', Auth::user()->id)->count();
        return response()->json($like_count, 200);
    }

    public function like_byUser_get()
    {
        $posts = Like::select('likes.*', 'posts.name', 'posts.category_id', 'posts.images', 'posts.user_id', 'users.name as username')
            ->join('posts', 'posts.id', 'likes.post_id')->join('users', 'posts.user_id', 'users.id')
            ->where('likes.user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get()->each(function ($query) {
            $query->images = json_decode($query->images);
            $query->removeBtn = true;
            return $query;
        });
        return response()->json($posts, 200);
    }

    // Comments
    public function add_comment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required',
            'description' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 200);
        }

        $comment = Comment::create([
            'post_id' => $request->post_id,
            'description' => $request->description,
            'user_id' => $request->user()->id,
        ]);
        $post = Post::select('user_id', 'name', 'id')->where('id', $request->post_id)->first();
        if ($post->user_id != Auth::user()->id) {
            $noti = Notification::create([
                'user_id' => $post->user_id,
                'value' => 'comment_id-' . $comment->id,
                'description' => '<a href="/profile/view/' . Auth::user()->id . '" >' . Auth::user()->name . '</a>' . ' commented on your <a href="/posts/' . $post->id . '">' . $post->name . '</a>',
            ]);
            $data = (object) [
                'user_id' => $noti->user_id,
                'body' => Auth::user()->name . ' commented on your post ' . $post->name . '.',
            ];
            event(new NotiEvent(json_encode($data)));
        }
        return response()->json(['success' => 'Commented!'], 200);
    }

    // Get Comment
    public function get_comment($id)
    {
        $comments = Comment::select('comments.*', 'users.name', 'users.profile_photo_path')->where('comments.post_id', $id)
            ->join('users', 'users.id', 'comments.user_id')->orderBy('created_at', 'desc')->get();
        return response()->json($comments, 200);
    }

    // Delete Comment
    public function delete_comment($id, Request $request)
    {
        Comment::where('id', $id)->where('user_id', Auth::user()->id)->delete();
        Notification::where('value', 'comment_id-' . $id)->delete();
        return response()->json(['success' => 'Comment deleted.'], 200);
    }

    //Who Like This Post
    public function who_like($id, Request $request)
    {
        $like_users = Like::select('likes.*', 'users.name', 'users.profile_photo_path')->where('likes.post_id', $id)
            ->join('users', 'users.id', 'likes.user_id')->get();
        return response()->json($like_users, 200);
    }
}
