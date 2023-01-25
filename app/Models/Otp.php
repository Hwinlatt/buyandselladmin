<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;
    protected $fillable = ['email','otp_code'];
    public static function otp_sent($user)
    {
        Otp::where('email',$user->email)->delete();
        $otp = Otp::create([
            'email'=>$user->email,
            'otp_code'=>random_int(100000, 999999),
        ]);
        return $otp;
    }

    public static function delete_email($email)
    {
        Otp::where('email', $email)->delete();
    }
}
