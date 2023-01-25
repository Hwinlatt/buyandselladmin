<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\VerificationEmail;
use App\Models\Otp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users,email,',
            'password' => 'required|min:6|same:confirmed_password',
            'region' => 'required',
            'city' => 'required',
            'gender'=>'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 200);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'region' => $request->region,
            'city' => $request->city,
            'gender'=>$request->gender,
        ]);

        $token = $user->createToken('buyandsell')->plainTextToken;
        return response()->json(['token' => $token, 'user' => $user], 200);

    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 200);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['error' => ['These credentials do not match our records.']], 200);
        }
        if (Hash::check($request->password, $user->password)) {
            $token = $user->createToken('vue-shop')->plainTextToken;
            $data = [
                'token' => $token,
                'user' => $user,
                'success'=>'Login Successful'
            ];
            return response()->json(['success'=>'Login Successful.','data' => $data], 200);
        } else {
            return response()->json(['error' => ['These credentials do not match our records.']], 200);
        }
    }

    // Logout Process
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['success'=>'Logout'], 200);
    }

    public function sentVerification(Request $request)
    {
        $user = $request->user();
        if ($user->email_verified_at) {
            return response()->json(['error' => ['Your Email is already Verified!']], 200);
        }
        $otp = Otp::otp_sent($user);
        Mail::to($user->email)->send(new VerificationEmail($user, $otp));
        return response()->json(['success' => 'Verification Mail was sent.'], 200);
    }

    public function makeVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp_code' => 'required|string|min:6|max:6',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()], 200);
        }
        $user = User::find($request->user()->id);
        if ($user->email_verified_at) {
            Otp::delete_email($request->user()->email);
            return response()->json(['error' => ['Your Email is already Verified!']], 200);
        }
        $otp = Otp::where('email', $request->user()->email)->get()->last();
        if ($otp) {
            if ($otp->created_at >= Carbon::now()->subMinutes(15)) {
                if ($otp->otp_code == $request->otp_code) {
                    $user->update([
                        'email_verified_at' => now(),
                    ]);
                    Otp::delete_email($request->user()->email);
                    return response()->json(['success' => 'Verified Email Success.'], 200);
                } else {
                    return response()->json(['error' => ['Otp Code does not match!']], 200);
                }
            } else {
                Otp::delete_email($request->user()->email);
                return response()->json(['error' => ['Otp Code is Expired!,Please request Otp Code again']], 200);
            }
        } else {
            return response()->json(['error' => ['Please request Otp Code again!']], 200);
        }
    }
}
