<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbandonedCart extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'email_sent',
        'abandoned_at',
    ];

    protected $casts = [
        'abandoned_at' => 'datetime',
        'email_sent'   => 'boolean',
    ];

    // belongs to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // belongs to Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}