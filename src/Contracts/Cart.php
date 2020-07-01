<?php

namespace WebApp\ShoppingCart\Contracts;

use illuminate\Support\Collection;
use WebApp\ShoppingCart\Cart as CartItem;

interface Cart
{
    public function get(): Collection;

    public function find(object $model): CartItem;

    public function addItem(object $model, int $quantity = 1): CartItem;

    public function removeItem(object $model): bool;

    public function addQuantity(object $model, int $quantity = 1): CartItem;

    public function removeQuantity(object $model, int $quantity = 1): CartItem;

    public function cartExists(object $model): bool;

    public function count(): int;
}