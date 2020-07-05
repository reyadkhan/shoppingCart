<?php

namespace WebApp\ShoppingCart;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use WebApp\ShoppingCart\Contracts\BuyableModel;
use WebApp\ShoppingCart\Exceptions\CartAlreadyExists;
use WebApp\ShoppingCart\Exceptions\CartNotFound;
use WebApp\ShoppingCart\Exceptions\InvalidCartQuantity;
use WebApp\ShoppingCart\Contracts\Cart;
use WebApp\ShoppingCart\Exceptions\InvalidModelInstance;

class CartManager implements Cart
{
    /**
     * @var SessionManager $sessionManager
     * @var array $configs
     */
    protected $sessionManager, $configs;

    /**
     * CartItem collection
     *
     * @var \Illuminate\Support\Collection
     */
    protected $cart;

    /**
     * CartItem count
     *
     * @var int
     */
    protected $count;

    /**
     * CartManager constructor.
     */
    public function __construct()
    {
        $this->configs = config('cart');
        $this->sessionManager = new SessionManager($this->configs['session_key']);
        $this->cart = collect($this->sessionManager->get());
        $this->count = $this->cart->count();
    }

    /**
     * Add new item to cart
     *
     * @param object $model
     * @param int $quantity
     * @return CartItem
     * @throws InvalidCartQuantity | InvalidModelInstance | CartAlreadyExists
     */
    public function add(object $model, int $quantity = 1): CartItem
    {
        $this->checkQuantity($model, $quantity);

        $cartItem = $this->createCartItem($model);

        if($cartItem->getQuantity() != $quantity) {
            $cartItem->setQuantity($quantity);
        }

        $this->cart->add($cartItem);
        $this->storeCart();
        return $cartItem;
    }

    /**
     * Delete a cart item
     *
     * @param object $model
     * @return bool
     * @throws InvalidModelInstance | CartNotFound
     */
    public function remove(object $model): bool
    {
        $collectionKey = $this->getKey($model);
        $this->removeByKey($collectionKey);
        return true;
    }

    /**
     * Update existing cart item
     *
     * @param object $model
     * @param int|null $quantity
     * @return CartItem
     * @throws InvalidCartQuantity | InvalidModelInstance | CartNotFound
     */
    public function update(object $model, int $quantity = null): CartItem
    {
        if( ! is_null($quantity)) {
            $this->checkQuantity($model, $quantity);
        }

        $cartItem = $this->getItem($model);

        foreach($cartItem->getAttributes() as $attribute => $value) {
            $cartItem->{$attribute} = $model->{$attribute};
        }

        if($quantity) {
            $cartItem->setQuantity($quantity);
        }

        $this->storeCart();
        return $cartItem;
    }

    /**
     * Remove from carts collection by key and store collection
     *
     * @param int $key
     * @return bool
     */
    protected function removeByKey(int $key): bool
    {
        $this->cart->forget($key);
        $this->storeCart();
        return true;
    }

    /**
     * Add cart quantity
     *
     * @param object $model
     * @param int $quantity
     * @return CartItem $updatedCart
     * @throws InvalidModelInstance | InvalidCartQuantity | CartNotFound
     */
    public function addQuantity(object $model, int $quantity = 1): CartItem
    {
        $this->checkQuantity($model, $quantity);

        $cartItem = $this->getItem($model);
        $cartItem->setQuantity($cartItem->getQuantity() + $quantity);
        $this->storeCart();
        return $cartItem;
    }

    /**
     * Remove cart quantity
     *
     * @param object $model
     * @param int $quantity
     * @return CartItem
     * @throws InvalidCartQuantity | InvalidModelInstance | CartNotFound
     */
    public function removeQuantity(object $model, int $quantity = 1): CartItem
    {
        $this->checkQuantity($model, $quantity);

        $cartItem = $this->getItem($model);
        $updatedQuantity = $cartItem->getQuantity() - $quantity;

        if($updatedQuantity <= 0) {
            $cartItem->setQuantity(0);
            $this->remove($model);
        } else {
            $cartItem->setQuantity($updatedQuantity);
            $this->storeCart();
        }

        return $cartItem;
    }

    /**
     * Check quantity if valid
     *
     * @param object $model
     * @param int $quantity
     * @throws InvalidCartQuantity
     */
    protected function checkQuantity(object $model, int $quantity): void
    {
        if($quantity <= 0) {
            throw InvalidCartQuantity::create(get_class($model), $quantity);
        }
    }

    /**
     * Create a new cart item
     *
     * @param object $model
     * @return CartItem
     * @throws InvalidModelInstance | CartAlreadyExists
     */
    protected function createCartItem(object $model): CartItem
    {
        if($this->itemExists($model)) {
            throw CartAlreadyExists::create(get_class($model), $model->getKey());
        }

        $modelName = get_class($model);
        $cartItem = new CartItem($model->getKey(), $modelName);
        $modelAttributeConfig = $this->configs['model_attributes'];

        if(array_key_exists($modelName, $modelAttributeConfig)) {
            foreach ($modelAttributeConfig[$modelName] as $attribute) {
                $cartItem->$attribute = $model->$attribute;
            }
        } else {
            foreach ($model->getAttributes() as $attribute => $value) {
                $cartItem->$attribute = $value;
            }
        }

        return $cartItem;
    }

    /**
     * Check the model object
     *
     * @param object $model
     * @throws InvalidModelInstance
     */
    protected function checkModel(object $model): void
    {
        if( ! $model instanceof Model && ! $model instanceof BuyableModel) {
            throw InvalidModelInstance::create(get_class($model));
        }
    }

    /**
     * Get a cart item
     *
     * @param object $model
     * @return CartItem
     * @throws InvalidModelInstance | CartNotFound
     */
    protected function getItem(object $model): CartItem
    {
        $item = $this->find($model);

        if( ! $item) {
            throw CartNotFound::create(get_class($model), $model->getKey());
        }
        return $item;
    }

    /**
     * Find a cart
     *
     * @param object $model
     * @return CartItem | null
     * @throws InvalidModelInstance
     */
    public function find(object $model)
    {
        $this->checkModel($model);

        return $this->cart->first(function($item) use ($model) {
            return $item->model_name == get_class($model) && $item->id == $model->getKey();
        });
    }

    /**
     * Find Carts collection key
     *
     * @param object $model
     * @return int
     * @throws InvalidModelInstance | CartNotFound
     */
    protected function getKey(object $model): int
    {
        $this->checkModel($model);

        $modelName = get_class($model);
        $modelKey = $model->getKey();

        $key = $this->cart->search(function ($item) use ($modelName, $modelKey) {
            return $item->model_name == $modelName && $item->id == $modelKey;
        });

        if($key === false) {
            throw CartNotFound::create($modelName, $modelKey);
        }
        return $key;
    }

    /**
     * Store carts to session
     *
     * @return void
     */
    protected function storeCart(): void
    {
        $this->sessionManager->add($this->cart);
        $this->count = $this->count();
    }

    /**
     * Destroy cart
     *
     * @return void
     */
    public function destroy(): void
    {
        $this->sessionManager->remove();
        $this->cart = collect();
        $this->count = 0;
    }

    /**
     * Get items count
     *
     * @return int
     */
    public function count(): int
    {
        return $this->cart->count();
    }

    /**
     * Get all cart items
     *
     * @return Collection
     */
    public function get(): collection
    {
        return $this->cart;
    }

    /**
     * Check item already exist
     *
     * @param object $model
     * @return bool
     * @throws InvalidModelInstance
     */
    public function itemExists(object $model): bool
    {
        return $this->find($model) ? true : false;
    }
}