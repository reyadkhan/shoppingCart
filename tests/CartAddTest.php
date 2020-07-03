<?php

namespace WebApp\ShoppingCart\Tests;

use WebApp\ShoppingCart\Exceptions\CartAlreadyExists;
use WebApp\ShoppingCart\Exceptions\InvalidCartQuantity;
use WebApp\ShoppingCart\Exceptions\InvalidModelInstance;
use WebApp\ShoppingCart\Facades\Cart;

class CartAddTest extends TestInit
{
    /**
     * @test
     */
    public function can_store_eloquent_model_cart()
    {
        Cart::addItem($this->model);
        $cartItem = Cart::find($this->model);
        $this->assertEquals($cartItem->id, $this->model->id);
    }

    /**
     * @test
     */
    public function can_exclude_attribute_according_to_config()
    {
        Cart::addItem($this->model);
        $cartItem = Cart::find($this->model);
        $this->assertEquals($cartItem->vendor, null);
    }

    /**
     * @test
     */
    public function can_include_attribute_according_to_config()
    {
        config()->set('cart.model_attributes.WebApp\ShoppingCart\Tests\Models\Product', [
            'name', 'price', 'image', 'vendor'
        ]);
        Cart::addItem($this->model);
        $cartItem = Cart::find($this->model);
        $this->assertEquals($cartItem->vendor, $this->model->vendor);
    }

    /**
     * @test
     */
    public function can_store_non_eloquent_model()
    {
        Cart::addItem($this->nonEloquentModel);
        $cartItem = Cart::find($this->nonEloquentModel);
        $this->assertEquals($cartItem->id, $this->nonEloquentModel->id);
        $this->assertEquals($cartItem->vendor, $this->nonEloquentModel->vendor);
    }

    /**
     * @test
     */
    public function can_add_quantity()
    {
        Cart::addItem($this->model);
        Cart::addQuantity($this->model, 3);
        $cartItem = Cart::find($this->model);
        $this->assertEquals(4, $cartItem->quantity);
    }

    /**
     * @test
     */
    public function can_return_updated_item()
    {
        Cart::addItem($this->model);
        $cartItem = Cart::addQuantity($this->model, 3);
        $this->assertEquals($this->model->id, $cartItem->id);
    }

    /**
     * @test
     */
    public function can_fail_invalid_quantity()
    {
        Cart::addItem($this->model);
        $this->expectException(InvalidCartQuantity::class);
        Cart::addQuantity($this->model, -1);
    }

    /**
     * @test
     */
    public function can_fail_non_buyable_model()
    {
        $this->expectException(InvalidModelInstance::class);
        Cart::addItem($this->nonBuyableModel);
    }

    /**
     * @test
     */
    public function can_fail_duplicate_model()
    {
        Cart::addItem($this->model);
        $this->expectException(CartAlreadyExists::class);
        Cart::addItem($this->model);
    }
}