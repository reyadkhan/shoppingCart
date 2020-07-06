<?php

namespace WebApp\ShoppingCart\Exceptions;

use InvalidArgumentException;

class CartAlreadyExists extends InvalidArgumentException
{
    /**
     * @param string $modelName
     * @param int|string $modelKey
     * @return CartAlreadyExists
     */
    public static function create(string $modelName, $modelKey)
    {
        return new static("Cart item with key {$modelKey} for the model {$modelName} already exist.");
    }
}