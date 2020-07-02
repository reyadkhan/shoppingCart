<?php
/**
 * Created by PhpStorm.
 * User: reyad
 * Date: 02-Jul-20
 * Time: 5:35 AM
 */

namespace WebApp\ShoppingCart\Tests\Models;


class NonBuyableProduct
{
    public $id, $name, $price, $vendor, $image;

    public function __construct($id, $name, $price, $vendor, $image)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->$vendor = $vendor;
        $this->$image = $image;
    }
}