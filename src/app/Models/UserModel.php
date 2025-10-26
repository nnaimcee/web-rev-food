<?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class UserModel extends Authenticatable
{
    use HasRoles;
    protected $table = 'users';
    protected $primaryKey = 'user_id'; // ตั้งให้ตรงกับชื่อจริงใน DB
    protected $fillable = ['username', 'email', 'password','role','avatar_img','created_at'];
    public $incrementing = true; // ถ้า primary key เป็นตัวเลข auto increment
    public $timestamps = false;
    protected $guard_name = 'web'; // กำหนด guard name สำหรับ Spatie Permission
}
