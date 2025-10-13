<?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class UserModel extends Authenticatable
{
    protected $table = 'users';
    protected $primaryKey = 'user_id'; // ตั้งให้ตรงกับชื่อจริงใน DB
    protected $fillable = ['username', 'email', 'password','role','avatar_img','create_at'];
    public $incrementing = true; // ถ้า primary key เป็นตัวเลข auto increment
    public $timestamps = false;
}
