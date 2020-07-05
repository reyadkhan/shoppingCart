<?php

namespace WebApp\ShoppingCart\Tests;


use WebApp\ShoppingCart\Exceptions\CartNotFound;
use WebApp\ShoppingCart\Exceptions\InvalidCartQuantity;
use WebApp\ShoppingCart\Facades\Cart;

class CartRemoveTest extends TestInit
{
    /**
     * @test
     */
    public function can_remove_item()
    {
        Cart::add($this->model);
        $this->assertEquals(true, Cart::itemExists($this->model));
        Cart::remove($this->model);
        $this->assertEquals(false, Cart::itemExists($this->model));
    }

    /**
     * @test
     */
    public function can_remove_quantity()
    {
        Cart::add($this->nonEloquentModel, 2);
        Cart::removeQuantity($this->nonEloquentModel);
        $this->assertEquals(1, Cart::find($this->nonEloquentModel)->quantity);
    }

    /**
     * @test
     */
    public function can_fail_if_quantity_invalid()
    {
        Cart::add($this->nonEloquentModel);
        $this->expectException(InvalidCartQuantity::class);
        Cart::removeQuantity($this->nonEloquentModel, -1);
    }

    /**
     * @test
     */
    public function can_fail_if_item_not_found()
    {
        Cart::add($this->nonEloquentModel);
        $this->expectException(CartNotFound::class);
        Cart::removeQuantity($this->model, 2);
    }

    /**
     * @test
     */
    public function can_remove_item_if_quantity_is_zero_or_less()
    {
        Cart::add($this->nonEloquentModel);
        Cart::removeQuantity($this->nonEloquentModel);
        $this->assertEquals(false, Cart::itemExists($this->nonEloquentModel));
    }

    /**
     * @test
     */
    public function can_return_currently_updated_item()
    {
        Cart::add($this->model);
        $this->assertEquals(true, Cart::itemExists($this->model));
        $removedItem = Cart::removeQuantity($this->model);
        $this->assertEquals($this->model->id, $removedItem->id);
    }
}