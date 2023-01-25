<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\SlideShow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $slides = SlideShow::all();
        $popular_posts = Post::orderBy('view', 'desc')->where('status', '1')->limit(8)->get()->each(function ($q) {
            $q->images = json_decode($q->images);
            return $q;
        });
        $data = [
            'slides' => $slides,
            'popular_posts' => $popular_posts,
        ];
        return response()->json($data, 200);
    }

    public function for_you(Request $request)
    {
        if (is_array($request->recentSearch)) {
            $for_you = $this->postSearch($request->recentSearch);
        }
        $data = [
            'for_you' => $for_you,
        ];
        return response()->json($data, 200);
    }

    private function postSearch($key)
    {
        $post = Post::where('user_id', '!=', Auth::user()->id)
            ->where('status', '1')->where(function ($q) use ($key) {
                foreach ($key as $k) {
                    $q->orWhere('name', 'like', '%' . $k . '%');
                    $q->orWhere('additional', 'like', '%' . $k . '%');
                    $q->orWhere('description', 'like', '%' . $k . '%');
                }
        })->orderBy('view', 'desc')->limit(16)->get()->each(function ($q) {
            $q->images = json_decode($q->images);
            return $q;
        });
        return $post;
    }
}
