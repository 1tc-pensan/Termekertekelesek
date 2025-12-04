<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Products extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'price',
    ];

    public function reviews()
    {
        return $this->hasMany(Reviews::class, 'product_id');
    }
}
