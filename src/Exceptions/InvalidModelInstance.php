<?php

namespace WebApp\ShoppingCart\Exceptions;

use InvalidArgumentException;

class InvalidModelInstance extends InvalidArgumentException
{
    /**
     * @param string $modelName
     * @return InvalidModelInstance
     */
    public static function create(string $modelName)
    {
        return new static("Invalid model instance. '{$modelName}' model should be instance of 'Illuminate\Database\Eloquent\Model' or 'WebApp\ShoppingCart\Contracts\Buyable'.");
    }
}