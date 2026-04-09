<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'email',
        'phone',
        'address',
        'city',
        'zip_code',
        'total_amount',
        'exchange_rate_usd',
        'payment_status',
        'shipping_status',
        'stripe_session_id',
        'paid_at',
    ];

    // One order has many items
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // An order belongs to a user
    public function user(){
        return $this->belongsTo(User::class);
    }
}
