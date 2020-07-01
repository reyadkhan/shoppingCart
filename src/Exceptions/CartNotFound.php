<?php

namespace WebApp\ShoppingCart\Exceptions;

use InvalidArgumentException;

class CartNotFound extends InvalidArgumentException
{
    public static function create(string $modelName, $modelKey)
    {
        return new static("Cart is not found for model {$modelName} with key {$modelKey}");
    }
}