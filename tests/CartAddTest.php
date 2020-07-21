<?php

namespace WebApp\ShoppingCart\Tests;

use WebApp\ShoppingCart\Exceptions\CartAlreadyExists;
use WebApp\ShoppingCart\Exceptions\CartNotFound;
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
        Cart::add($this->model);
        $cartItem = Cart::find($this->model);
        $this->assertEquals($cartItem->id, $this->model->id);
    }

    /**
     * @test
     */
    public function can_exclude_attribute_according_to_config()
    {
        Cart::add($this->model);
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
        Cart::add($this->model);
        $cartItem = Cart::find($this->model);
        $this->assertEquals($cartItem->vendor, $this->model->vendor);
    }

    /**
     * @test
     */
    public function can_store_non_eloquent_model()
    {
        Cart::add($this->nonEloquentModel);
        $cartItem = Cart::find($this->nonEloquentModel);
        $this->assertEquals($cartItem->id, $this->nonEloquentModel->id);
        $this->assertEquals($cartItem->vendor, $this->nonEloquentModel->vendor);
    }

    /**
     * @test
     */
    public function can_update_cart_item()
    {
        Cart::add($this->model);
        $updatedItem = Cart::update($this->model, 3);
        $this->assertEquals(3, $updatedItem->quantity);
    }

    /**
     * @test
     */
    public function can_fail_update_if_item_not_exists()
    {
        Cart::add($this->model);
        $this->expectException(CartNotFound::class);
        Cart::update($this->nonEloquentModel, 3);
    }

    /**
     * @test
     */
    public function can_fail_update_if_provide_invalid_model()
    {
        Cart::add($this->model);
        $this->expectException(InvalidModelInstance::class);
        Cart::update($this->nonBuyableModel, 3);
    }

    /**
     * @test
     */
    public function can_add_quantity()
    {
        Cart::add($this->model);
        Cart::addQuantity($this->model, 3);
        $cartItem = Cart::find($this->model);
        $this->assertEquals(4, $cartItem->quantity);
    }

    /**
     * @test
     */
    public function can_return_updated_item()
    {
        Cart::add($this->model);
        $cartItem = Cart::addQuantity($this->model, 3);
        $this->assertEquals($this->model->id, $cartItem->id);
    }

    /**
     * @test
     */
    public function can_fail_invalid_quantity()
    {
        Cart::add($this->model);
        $this->expectException(InvalidCartQuantity::class);
        Cart::addQuantity($this->model, -1);
    }

    /**
     * @test
     */
    public function can_fail_non_buyable_model()
    {
        $this->expectException(InvalidModelInstance::class);
        Cart::add($this->nonBuyableModel);
    }

    /**
     * @test
     */
    public function can_fail_duplicate_model()
    {
        Cart::add($this->model);
        $this->expectException(CartAlreadyExists::class);
        Cart::add($this->model);
    }
    
    /**
     * @test
     */
    public function can_set_discount()
    {
        Cart::add($this->model, 2);
        Cart::setDiscount(10);
        $this->assertEquals(10, Cart::getDiscount());
    }
}