<?php

namespace WebApp\ShoppingCart\Tests;


use WebApp\ShoppingCart\Exceptions\InvalidModelInstance;
use WebApp\ShoppingCart\Facades\Cart;

class CartReadTest extends TestInit
{
    /**
     * @test
     */
    public function can_get_all_items()
    {
        Cart::add($this->model);
        Cart::add($this->nonEloquentModel);
        $this->assertEquals(2, Cart::get()->count());
    }

    /**
     * @test
     */
    public function can_find_single_item()
    {
        Cart::add($this->nonEloquentModel);
        $this->assertEquals($this->nonEloquentModel->id, Cart::find($this->nonEloquentModel)->id);
    }

    /**
     * @test
     */
    public function can_return_null_if_item_does_not_find()
    {
        Cart::add($this->nonEloquentModel);
        $this->assertNull(Cart::find($this->model));
    }

    /**
     * @test
     */
    public function can_count_cart_item()
    {
        Cart::add($this->model);
        Cart::add($this->nonEloquentModel);
        $this->assertEquals(2, Cart::count());
    }
    
    /**
     * @test
     */
    public function can_get_total_price()
    {
        Cart::add($this->model, 2);
        Cart::add($this->nonEloquentModel);
        $this->assertEquals(($this->model->price * 2) + $this->nonEloquentModel->price_vat_inc, Cart::total());
    }

    /**
     * @test
     */
    public function can_check_if_item_exists()
    {
        Cart::add($this->model);
        $this->assertEquals(true, Cart::itemExists($this->model));
    }

    /**
     * @test
     */
    public function can_check_if_item_not_exists()
    {
        Cart::add($this->model);
        $this->assertEquals(false, Cart::itemExists($this->nonEloquentModel));
    }

    /**
     * @test
     */
    public function can_fail_item_exists_with_invalid_model()
    {
        Cart::add($this->model);
        $this->expectException(InvalidModelInstance::class);
        Cart::itemExists($this->nonBuyableModel);
    }

    /**
     * @test
     */
    public function can_destroy_cart()
    {
        Cart::add($this->nonEloquentModel);
        Cart::add($this->model);
        $this->assertEquals(2, Cart::count());
        Cart::destroy();
        $this->assertEquals(true, Cart::get()->isEmpty());
    }
    
    /**
     * @test
     */
    public function can_calculate_discount()
    {
        Cart::add($this->model, 2);
        Cart::setDiscount(10);
        $subTotal = Cart::subTotal();
        $totalAmount = round($subTotal - ($subTotal * 10 / 100), 2);
        $this->assertEquals($totalAmount, Cart::total());
    }
}