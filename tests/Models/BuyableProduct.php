<?php

namespace WebApp\ShoppingCart\Tests\Models;

use WebApp\ShoppingCart\Contracts\BuyableModel;
use WebApp\ShoppingCart\Traits\Buyable;

class BuyableProduct implements BuyableModel
{
    use Buyable;

    public $id, $name, $price, $price_vat_inc, $vendor, $image;

    public function __construct($id, $name, $price, $vendor, $image)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->price_vat_inc = $price + ($price * 21 / 100);
        $this->$vendor = $vendor;
        $this->$image = $image;
    }
}