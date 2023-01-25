<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'images',
        'name',
        'category_id',
        'price',
        'adjust_price',
        'additional',
        'description',
        'status',
        'view',
        'mmk'
    ];

    public function category_name()
    {
        return $this->hasOne(Category::class,'id','category_id');
    }
}
