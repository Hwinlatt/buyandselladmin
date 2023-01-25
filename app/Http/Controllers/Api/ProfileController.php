<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function index($id)
    {
        $user = User::find($id);
        $total_post = Post::where('user_id',$id)->count();
        $categories = Post::select('category_id',DB::raw('count(*) as count'))->groupBy('category_id')->get();
        $data = [
            'user'=>$user,
            'total_post'=>$total_post,
            'categories'=>$categories,
        ];
        return response()->json($data, 200);
    }
}
