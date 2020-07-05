<?php

namespace WebApp\ShoppingCart\Exceptions;

use InvalidArgumentException;

class InvalidCartQuantity extends InvalidArgumentException
{
    /**
     * @param string $modelName
     * @param int $quantity
     * @return InvalidCartQuantity
     */
    public static function create(string $modelName, int $quantity)
    {
        return new static("Invalid cart quantity '{$quantity}' for model {$modelName}. Quantity should be greater than 0.");
    }
}