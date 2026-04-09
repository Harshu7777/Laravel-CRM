<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'description',
        'price',
        'sale_price',
        'cost_price',
        'stock_quantity',
        'category_id',
        'image',
        'additional_images',
        'is_active',
    ];

    protected $casts = [
        'additional_images' => 'array', // Automatically cast JSON to array
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
