<?php

namespace App\Http\Controllers\Api;

use App\Events\NotiEvent;
use App\Http\Controllers\Controller;
use App\Models\Follow;
use App\Models\Notification;
use App\Models\Post;
use App\Models\ReviewUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index($id)
    {
        $user = User::find($id);
        $is_follow = Follow::where('user_id', Auth::user()->id)->where('follow_to', $id)->count();
        $total_post = Post::where('user_id', $id)->count();
        $categories = Post::select('category_id', DB::raw('count(*) as count'))->groupBy('category_id')->get();
        $review_count = ReviewUser::where('rate_user_id', $id)->count();
        $review = (object) [
            'count' => $review_count,
            'rating' => $this->average_rating($id),
        ];
        $data = [
            'user' => $user,
            'total_post' => $total_post,
            'categories' => $categories,
            'review' => $review,
            'follow_status' => $is_follow > 0 ? 1 : 2,
        ];
        return response()->json($data, 200);
    }

    public function show_review($id)
    {
        $reviews = ReviewUser::select('review_users.*', 'users.name', 'users.profile_photo_path')->where('rate_user_id', $id)
            ->join('users', 'users.id', 'review_users.user_id')->orderBy('created_at', 'desc')->get();
        $user = User::find($id);
        $is_reviewed = ReviewUser::where('rate_user_id', $id)->where('user_id', Auth::user()->id)->count();
        $data = [
            'reviews' => $reviews,
            'infoUser' => $user,
            'is_reviewed' => $is_reviewed,
            'rating' => $this->average_rating($id),
        ];
        return response()->json($data, 200);
    }

    public function add_review(Request $request)
    {
        $validator = $this->review_validator($request);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 200);
        }
        $check_is_reviewed = ReviewUser::where('rate_user_id', $request->rate_user_id)->where('user_id', Auth::user()->id)->first();
        if ($check_is_reviewed) {
            $check_is_reviewed->delete();
        }
        ReviewUser::create([
            'rate_user_id' => $request->rate_user_id,
            'rating' => $request->rating,
            'user_id' => Auth::user()->id,
            'description' => $request->description,
        ]);
        return response()->json(['success' => 'The review is added to the user profile.'], 200);
    }

    //Follow To User
    public function follow($id)
    {
        $count = Follow::where('user_id', Auth::user()->id)->where('follow_to', $id)->count();
        if ($count == 0) {
            $follow = Follow::create([
                'user_id' => Auth::user()->id,
                'follow_to' => $id,
            ]);
            Notification::create([
                'user_id' => $id,
                'value' => 'followed_id-' . $follow->id,
                'description' => '<a href="/profile/view/' . Auth::user()->id . '" >' . Auth::user()->name . '</a>' . ' followed you.',
            ]);
            $data = (object) [
                'user_id' => $id,
                'body' => Auth::user()->name . ' is followed you.',
            ];
            event(new NotiEvent(json_encode($data)));
            return response()->json(['success' => '1'], 200);
        } else {
            $follow = Follow::where('user_id', Auth::user()->id)->where('follow_to', $id)->first();
            Notification::where('value','followed_id-' .$follow->id)->delete();
            $follow->delete();
            return response()->json(['success' => '2'], 200);
        }
    }

    private function average_rating($id)
    {
        $review_count = ReviewUser::where('rate_user_id', $id)->count();
        $total_rating = ReviewUser::select('rating', DB::raw('SUM(rating) as total_rate'))->where('rate_user_id', $id)
            ->GroupBy('rate_user_id')->get();
        if ($review_count > 0) {
            $rate = $total_rating[0]->total_rate / $review_count;
            return number_format($rate, 1);
        }
        return 0.0;
    }

    public function remove_review($id)
    {
        ReviewUser::where('user_id', Auth::user()->id)->where('id', $id)->delete();
        return response()->json(['success' => 'The review has been deleted'], 200);
    }

    private function review_validator($request)
    {
        $validator = Validator::make($request->all(), [
            'rate_user_id' => 'required',
            'rating' => 'required|numeric|min:1|max:5',
            'description' => 'required',
        ]);
        return $validator;
    }
}
