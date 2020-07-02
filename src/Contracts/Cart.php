<?php

namespace WebApp\ShoppingCart\Contracts;

use illuminate\Support\Collection;
use WebApp\ShoppingCart\CartItem;
use WebApp\ShoppingCart\Exceptions\CartAlreadyExists;
use WebApp\ShoppingCart\Exceptions\CartNotFound;
use WebApp\ShoppingCart\Exceptions\InvalidCartQuantity;
use WebApp\ShoppingCart\Exceptions\InvalidModelInstance;

interface Cart
{
    /**
     * Get all cart item
     *
     * @return Collection
     */
    public function get(): Collection;

    /**
     * Find a cart item
     *
     * @param object $model
     * @return CartItem
     * @throws InvalidModelInstance | CartNotFound
     */
    public function find(object $model): CartItem;

    /**
     * Add new item to cart
     *
     * @param object $model
     * @param int $quantity
     * @return CartItem
     * @throws InvalidModelInstance | CartAlreadyExists
     */
    public function addItem(object $model, int $quantity = 1): CartItem;

    /**
     * Remove an item from cart
     *
     * @param object $model
     * @return bool
     * @throws InvalidModelInstance | CartNotFound
     */
    public function removeItem(object $model): bool;

    /**
     * Add quantity of existing item
     *
     * @param object $model
     * @param int $quantity
     * @return CartItem
     * @throws CartNotFound | InvalidCartQuantity | InvalidModelInstance
     */
    public function addQuantity(object $model, int $quantity = 1): CartItem;

    /**
     * Remove/decrease quantity of existing item
     *
     * @param object $model
     * @param int $quantity
     * @return CartItem
     * @throws InvalidCartQuantity | CartNotFound | InvalidModelInstance
     */
    public function removeQuantity(object $model, int $quantity = 1): CartItem;

    /**
     * Check if cartItem exist in the cart
     *
     * @param object $model
     * @return bool
     */
    public function itemExists(object $model): bool;

    /**
     * Cart item count
     *
     * @return int
     */
    public function count(): int;

    /**
     * Destroy cart
     */
    public function destroy(): void;
}