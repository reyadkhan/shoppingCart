<?php

namespace WebApp\ShoppingCart;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
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
     * Current cart item
     *
     * @var CartItem
     */
    protected $cartItem;

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
     * @throws CartAlreadyExists
     */
    public function addItem(object $model, int $quantity = 1): CartItem
    {
        if($this->itemExists($model)) {
            throw CartAlreadyExists::create(get_class($model), $model->getKey());
        }

        $cartItem = $this->createCartItem($model);

        if($this->cartItem->getQuantity() != $quantity) {
            $this->cartItem->setQuantity($quantity);
        }

        $this->storeCart();
        return $cartItem;
    }

    /**
     * Delete a cart item
     *
     * @param object $model
     * @return bool
     */
    public function removeItem(object $model): bool
    {
        $collectionKey = $this->findKey($model);
        $this->removeByKey($collectionKey);
        return true;
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
     * @throws InvalidCartQuantity
     */
    public function addQuantity(object $model, int $quantity = 1): CartItem
    {
        $this->checkQuantity($model, $quantity);

        $collectionKey = $this->findKey($model);
        $this->cartItem = $this->cart->pull($collectionKey);
        $currentQuantity = $this->cartItem->getQuantity();
        $this->cartItem->setQuantity($currentQuantity + $quantity);
        $updatedCartItem = $this->cartItem;
        $this->storeCart();
        return $updatedCartItem;
    }

    /**
     * Remove cart quantity
     *
     * @param object $model
     * @param int $quantity
     * @return CartItem
     */
    public function removeQuantity(object $model, int $quantity = 1): CartItem
    {
        $this->checkQuantity($model, $quantity);

        $collectionKey = $this->findKey($model);
        $cartItem = $this->cart->pull($collectionKey);
        $currentQuantity = $cartItem->getQuantity();
        $updatedQuantity = $currentQuantity - $quantity;

        if($updatedQuantity <= 0) {
            $cartItem->setQuantity(0);
        } else {
            $cartItem->setQuantity($updatedQuantity);
            $this->cartItem = $cartItem;
        }

        $this->storeCart();
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
            throw InvalidCartQuantity::create(get_class($model), $model->getKey(), $quantity);
        }
    }

    /**
     * Create a new cart item
     *
     * @param object $model
     * @return CartItem
     */
    protected function createCartItem(object $model): CartItem
    {
        $this->checkModel($model);

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

        return $this->cartItem = $cartItem;
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
     * Find a cart
     *
     * @param object $model
     * @return CartItem
     * @throws CartNotFound
     */
    public function find(object $model): CartItem
    {
        $this->checkModel($model);

        $modelName = get_class($model);
        $modelKey = $model->getKey();

        $cartItem = $this->cart->first(function($item) use ($modelName, $modelKey) {
            return $item->model_name == $modelName && $item->id == $modelKey;
        });

        if( ! $cartItem) {
            throw CartNotFound::create($modelName, $modelName);
        }
        return $cartItem;
    }

    /**
     * Find Carts collection key
     *
     * @param object $model
     * @return int
     * @throws CartNotFound
     */
    protected function findKey(object $model): int
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
    public function storeCart(): void
    {
        if($this->cartItem) {
            $this->cart->add($this->cartItem);
            $this->cartItem = null;
        }

        $this->sessionManager->add($this->cart);
        $this->count = $this->cart->count();
    }

    /**
     * Destroy cart
     *
     * @return void
     */
    public function destroy(): void
    {
        $this->sessionManager->remove();
        $this->cartItem = null;
        $this->cart = collect();
    }

    /**
     * Get items count
     *
     * @return int
     */
    public function count(): int
    {
        return $this->count;
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
     */
    public function itemExists(object $model): bool
    {
        if( ! $this->isValidModel($model)) {
            return false;
        }

        return $this->cart->contains(function ($item) use ($model) {
            return $item->model_name == get_class($model) && $item->id == $model->getKey();
        });
    }

    /**
     * Check if model is valid
     *
     * @param object $model
     * @return bool
     */
    protected function isValidModel(object $model): bool
    {
        try{
            $this->checkModel($model);
        } catch (InvalidModelInstance $e) {
            report($e);
            return false;
        }
        return true;
    }
}