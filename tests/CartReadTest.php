<?php

namespace WebApp\ShoppingCart\Tests;


use WebApp\ShoppingCart\Facades\Cart;

class CartReadTest extends TestInit
{
    /**
     * @test
     */
    public function can_get_all_items()
    {
        Cart::addItem($this->model);
        Cart::addItem($this->nonEloquentModel);
        $this->assertEquals(2, Cart::get()->count());
    }

    /**
     * @test
     */
    public function can_find_single_item()
    {
        Cart::addItem($this->nonEloquentModel);
        $this->assertEquals($this->nonEloquentModel->id, Cart::find($this->nonEloquentModel)->id);
    }

    /**
     * @test
     */
    public function can_count_cart_item()
    {
        Cart::addItem($this->model);
        Cart::addItem($this->nonEloquentModel);
        $this->assertEquals(2, Cart::count());
    }

    /**
     * @test
     */
    public function can_check_if_item_exists()
    {
        Cart::addItem($this->model);
        $this->assertEquals(true, Cart::itemExists($this->model));
    }

    /**
     * @test
     */
    public function can_check_item_exist_with_invalid_model()
    {
        Cart::addItem($this->model);
        $this->assertEquals(false, Cart::itemExists($this->nonBuyableModel));
    }

    /**
     * @test
     */
    public function can_destroy_cart()
    {
        Cart::addItem($this->nonEloquentModel);
        Cart::addItem($this->model);
        $this->assertEquals(2, Cart::count());
        Cart::destroy();
        $this->assertEquals(true, Cart::get()->isEmpty());
    }
}