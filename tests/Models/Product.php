<?php

namespace WebApp\ShoppingCart\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['price', 'name', 'image', 'vendor'];
}