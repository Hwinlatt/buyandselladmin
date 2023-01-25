<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user_count = User::where('role','user')->count();
        $today_singUp = User::whereDate('created_at',Carbon::today())->count();
        $report_count = Report::select('*',DB::raw('count(*) as count'))->groupBy('report_type')->get();
        $post_count = Post::count();
        $today_post = Post::whereDate('created_at',Carbon::today())->count();
        $data = (object) [
            'user_count' => $user_count,
            'report_count'=> $report_count,
            'post_count'=>$post_count,
            'today_post'=>$today_post,
            'today_singUp'=>$today_singUp,
        ];
        return view('dashboard',compact('data'));
    }
}
