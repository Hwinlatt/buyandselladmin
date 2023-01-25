<?php

namespace App\Models;

use App\Models\Like;
use App\Models\Post;
use App\Models\Report;
use App\Models\Comment;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Jetstream\HasProfilePhoto;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'region',
        'city',
        'phone',
        'description',
        'role',
        'email_verified_at',
        'profile_photo_path',
        'password',
        'gender',
        'background_img'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public static function deleteUser($id)
    {
        User::find($id)->delete();
        Post::where('user_id',$id)->delete();
        Like::where('user_id',$id)->delete();
        Comment::where('user_id',$id)->delete();
        Report::where('sent_user_id')->delete();
        Report::where('report_id',$id)->where('type','user')->delete();
    }
}
