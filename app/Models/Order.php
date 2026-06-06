<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'store_id',
        'user_id',
        'status',
        'total_price',
        'notes'
    ];

    public function items(){
        return $this->hasMany(OrderItem::class);
    }
    public function store(){
        return $this->belongsTo(Store::class);
    }
    public function customer(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
