<?php

namespace WebApp\ShoppingCart;

use Illuminate\Support\ServiceProvider;
use WebApp\ShoppingCart\Contracts\Cart;

class ShoppingCartServiceProvider extends ServiceProvider
{
    /**
     * Register bindings
     *
     * @var array
     */
    public $bindings = [
        Cart::class => CartManager::class
    ];

    /**
     * Register application services
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/cart.php', 'cart');

        $this->app->bind('cart', CartManager::class);
    }

    /**
     * Bootstrap the application events
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/cart.php' => config_path('cart.php')
        ], 'config');
    }
}