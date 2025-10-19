<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $table = 'restaurants';
    protected $primaryKey = 'restaurant_id';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'address',
        'description',
        'image_path',
    ];

    public function reviews()
    {
        return $this->hasMany(Review::class, 'restaurant_id', 'restaurant_id');
    }
}
