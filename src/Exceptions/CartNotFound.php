<?php

namespace WebApp\ShoppingCart\Exceptions;

use InvalidArgumentException;

class CartNotFound extends InvalidArgumentException
{
    /**
     * @param string $modelName
     * @param $modelKey
     * @return CartNotFound
     */
    public static function create(string $modelName, $modelKey)
    {
        return new static("Cart item is not found for model {$modelName} with key {$modelKey}", 404);
    }
}