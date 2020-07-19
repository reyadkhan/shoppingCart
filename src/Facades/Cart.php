<?php

namespace WebApp\ShoppingCart\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use WebApp\ShoppingCart\CartItem;

/**
 * @method static Collection get()
 * @method static CartItem find(object $model)
 * @method static CartItem add(object $model, int $quantity = 1, array $options = [])
 * @method static bool remove(object $model)
 * @method static CartItem update(object $model, int $quantity = null, array $options = [])
 * @method static CartItem addQuantity(Object $model, int $quantity = 1)
 * @method static CartItem removeQuantity(object $model, int $quantity = 1)
 * @method static float total(int $precision = 2)
 * @method static bool itemExists(object $model)
 * @method static int count()
 * @method static void destroy()
 *
 * @see \WebApp\ShoppingCart\CartManager
 * @see \WebApp\ShoppingCart\Contracts\Cart
 */
class Cart extends Facade
{
    /**
     * Facade accessor
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'cart';
    }
}