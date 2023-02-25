<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'=>'required|string|max:254',
            'region'=>'required',
            'city'=>'required',
            'phone'=>'string|max:254',
            'description'=>'string|max:4294967290'
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()], 200);
        }
        $user = User::find($request->user()->id);
        $user->update([
            'name'=>$request->name,
            'region'=>$request->region,
            'city'=>$request->city,
            'phone'=>$request->phone,
            'description'=>$request->description,
            'gender'=>$request->gender,
        ]);
        return  response()->json(['success'=>'Profile Updated!','user'=>$user], 200);
    }

    public function updateImage(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'image'=>'required|image',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()], 200);
        }
        $user = User::find($request->user()->id);
        $path = 'storage/images/'.$user->profile_photo_path;
        $fileName = $this->imageStoreProcess($path,$request);
        $user->update([
            'profile_photo_path' => $fileName,
        ]);
        return response()->json(['success'=>'Profile image changed.','user'=>$user], 200);
    }

    public function updateCoverImg(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'image'=>'required|image',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()], 200);
        }
        $user = User::find($request->user()->id);
        $path = 'storage/images/'.$user->background_img;
        $fileName = $this->imageStoreProcess($path,$request);
        $user->update([
            'background_img' => $fileName,
        ]);
        return response()->json(['success'=>'Background image changed.','user'=>$user], 200);
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'old_password'=>'required|min:6',
            'new_password'=>'required|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()], 200);
        }
        $user = User::find($request->user()->id);
        if (Hash::check($request->old_password,$user->password)) {
            if (Hash::check($request->new_password,$user->password)) {
                return response()->json(['error'=>["New password can't be the same as old password"]], 200);
            }else{
                $user->update([
                    'password'=>Hash::make($request->new_password),
                ]);
                return response()->json(['success'=>'Password Changed.'], 200);
            }
        }else{
            return response()->json(['error'=>['Old Password does not math!.']], 200);
        }
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email'=>'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()], 200);
        }
        $user = User::where('email',$request->email)->first();
        if (!$user) {
            return response()->json(['error'=>['Cannot found user with this email.Please Sing up first!']]);
        }
        $response  = Password::sendResetLink($user->only('email'));
        if ($response == Password::RESET_LINK_SENT) {
            return response()->json(['success'=>'Password reset link is sent to '.$user->email .'.'], 200);
        }else{
            return response()->json(['error'=>['Something was wrong.Please Refresh!']], 200);
        }
    }

    //Go To Messenger Page of Another Url Make On user Process
    public function messenger(Request $request)
    {
        # code...
    }

    private function imageStoreProcess($path,$request){
        if (File::exists($path)) {
            File::delete($path);
        }
        $fileName = time().'-'.uniqid().'.'.$request->file('image')->getClientOriginalExtension();
        $request->file('image')->storeAs('public/images',$fileName);
        return $fileName;
    }
}
