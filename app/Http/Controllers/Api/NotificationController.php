<?php

namespace App\Http\Controllers\Api;

use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notification = Notification::where('user_id',Auth::user()->id)->orderBy('id','desc')->get();
        return response()->json($notification, 200);
    }

    public function count()
    {
        $count = Notification::where('user_id',Auth::user()->id)->
        where('status',NULL)->count();
        return response()->json($count, 200);
    }

    public function all_read()
    {
        $notification = Notification::where('user_id',Auth::user()->id)->get();
        if ($notification) {
            foreach ($notification as $noti ) {
                $noti->update([
                    'status'=>'read'
                ]);
            }
        }
        return response()->json(['success'=>'Success'], 200);
    }

    public function destroy(Request $request)
    {
        if ($request->id) {
            Notification::where('id',$request->id)->where('user_id',Auth::user()->id)->delete();
        }else{
            Notification::where('user_id',Auth::user()->id)->delete();
        }
        return response()->json(['success'=>'Success'], 200);
    }
}
