<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $table = 'reviews';
    protected $primaryKey = 'review_id'; // 👈 ระบุ primary key ให้ถูกต้อง
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'review_id',
        'menu_name',
        'comment',
        'rating',
        'image_path',
        'restaurant_id',
        'menu_id',
        'user_id',
    ];
}
