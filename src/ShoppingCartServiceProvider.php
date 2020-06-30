<?php
/**
 * Created by PhpStorm.
 * User: reyad
 * Date: 30-Jun-20
 * Time: 12:31 PM
 */

namespace WebApp\ShoppingCart;


use Illuminate\Support\ServiceProvider;

class ShoppingCartServiceProvider extends ServiceProvider
{
    /**
     * Register application services
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/cart.php', 'cart');
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