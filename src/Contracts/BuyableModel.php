<?php

namespace WebApp\ShoppingCart\Contracts;


interface BuyableModel
{
    /**
     * Get buyable class primary key
     *
     * @return int|string
     */
    public function getKey();

    /**
     * Get buyable class attributes
     *
     * @return array
     */
    public function getAttributes(): array;
}