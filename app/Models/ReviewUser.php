<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewUser extends Model
{
    use HasFactory;
    protected $fillable =
        ['user_id',
        'rate_user_id',
        'rating',
        'description'];
}
