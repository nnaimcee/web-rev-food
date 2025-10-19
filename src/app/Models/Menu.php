<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menus';
    protected $primaryKey = 'menu_id';
    public $timestamps = false;

    protected $fillable = [
        'restaurant_id',
        'name',
        'price',
        'description',
        'menu_img',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id', 'restaurant_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'menu_id', 'menu_id');
    }
}
