<?php

namespace WebApp\ShoppingCart\Collections;

use Illuminate\Support\Collection;

class CartCollection extends Collection
{
    public $discount;
    
    public function __construct($items = [])
    {
        if($items instanceof $this) {
            $this->discount = $items->discount;
        }
        parent::__construct($items);
    }
}