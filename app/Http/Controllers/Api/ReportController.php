<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'report_id'=>'required',
            'report_type'=>'required',
            'type'=>'required',
            'description'=>'required|string|min:5',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()], 200);
        }
        $db_chk = Report::where('sent_user_id',Auth::user()->id)->where('type',$request->type)
        ->where('report_type',$request->report_type)->count();
        if ($db_chk > 0) {
            return response()->json(['error'=>['You have been reported!']], 200);
        }
        Report::create([
            'sent_user_id'=>Auth::user()->id,
            'report_id'=>$request->report_id,
            'report_type'=>$request->report_type,
            'type'=>$request->type,
            'description'=>$request->description,
        ]);

        return response()->json(['success'=>'Reported to Admin'], 200);
    }
}
