<?php

namespace WebApp\ShoppingCart\Facades;


use Illuminate\Support\Facades\Facade;

class Cart extends Facade
{
    /**
     * Facade accessor
     *
     * @see WebApp\ShoppingCart\Contracts\Cart
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'cart';
    }
}