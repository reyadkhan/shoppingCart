<?php

namespace WebApp\ShoppingCart\Tests;

use Orchestra\Testbench\TestCase;
use WebApp\ShoppingCart\Facades\Cart;
use WebApp\ShoppingCart\Tests\Models\BuyableProduct;
use WebApp\ShoppingCart\Tests\Models\NonBuyableProduct;
use WebApp\ShoppingCart\Tests\Models\Product;

class TestInit extends TestCase
{
    public $model, $nonEloquentModel, $nonBuyableModel;

    /**
     * Pre setup
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->model = new Product([
            'name' => 'Product name',
            'price' => 500,
            'image' =>'storage/cart',
            'vendor' => 'Vendor name'
        ]);
        $this->model->id = 22;

        $this->nonEloquentModel = new BuyableProduct(
            11, 'buyable product', 200, 'vendor name', 'storage/public');
        $this->nonBuyableModel = new NonBuyableProduct(
            13, 'buyable product', 200, 'vendor name', 'storage/public');
    }

    /**
     * Package provider
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return ['WebApp\ShoppingCart\ShoppingCartServiceProvider'];
    }

    /**
     * Load aliases
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Cart' => Cart::class
        ];
    }

    /**
     * Setup environment
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('cart', [
            'session_key' => 'webAppShoppingCart',

            'model_attributes' => [

                'WebApp\ShoppingCart\Tests\Models\Product' => [
                    'name', 'price', 'image'
                ]
            ]
        ]);
    }
}