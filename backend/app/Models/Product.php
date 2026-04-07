<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Product extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'products';

    protected $fillable = [
        'provider_id',
        'provider_product_id',
        'name',
        'category_id',
        'price',
        'base_price',
        'profit_percent',
        'params',
        'qty_values',
        'available',
        'image',
        'product_type',
    ];

    protected $casts = [
        'provider_id' => 'string',
        'provider_product_id' => 'string',
        'category_id' => 'string',
        'price' => 'array',
        'base_price' => 'array',
        'profit_percent' => 'float',
        'params' => 'array',
        'qty_values' => 'array',
        'available' => 'boolean',
        'product_type' => 'string',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', '_id');
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class, 'provider_id', '_id');
    }
}
