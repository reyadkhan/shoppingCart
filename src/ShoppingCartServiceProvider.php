<?php

namespace WebApp\ShoppingCart;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use WebApp\ShoppingCart\Contracts\Cart;
use WebApp\ShoppingCart\Facades\Cart as CartFacade;

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

        AliasLoader::getInstance()->alias('Cart', CartFacade::class);
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