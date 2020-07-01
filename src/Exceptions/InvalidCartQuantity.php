<?php

namespace WebApp\ShoppingCart\Exceptions;

use InvalidArgumentException;

class InvalidCartQuantity extends InvalidArgumentException
{
    public static function create(string $modelName, $modelKey, int $quantity)
    {
        return new static("Invalid cart quantity '{$quantity}' for model {$modelName} key '{$modelKey}'. Quantity should be greater than 0.");
    }
}