<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'address',
        'category',
        'logo',
        'is_open'
    ];

    protected $casts = ['is_open'=>'boolean'];

    public function owner(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function products(){
        return $this->hasMany(Product::class);
    }
    public function orders(){
        return $this->hasMany(Order::class);
    }
}
