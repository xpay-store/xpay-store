<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Category extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'categories';

    protected $fillable = [
        'name',
        'parent_id',
        'image',
        'order',
        'active',
        'provider_category_id',
    ];

    protected $casts = [
        'parent_id' => 'string',
        'order' => 'integer',
        'active' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', '_id');
    }
}
