<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'sent_user_id',
        'report_id',
        'report_type',
        'description',
    ];

    public function report_from()
    {
        return $this->hasOne(User::class,'id','sent_user_id');
    }

    public function report_user()
    {
        return $this->hasOne(User::class,'id','report_id');
    }

    public function report_post()
    {
        return $this->hasOne(Post::class,'id','report_id');
    }
}
