<?php

namespace WebApp\ShoppingCart;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use WebApp\ShoppingCart\Contracts\BuyableModel;
use WebApp\ShoppingCart\Exceptions\CartAlreadyExists;
use WebApp\ShoppingCart\Exceptions\CartNotFound;
use WebApp\ShoppingCart\Exceptions\InvalidCartQuantity;
use WebApp\ShoppingCart\Contracts\Cart as Contract;
use WebApp\ShoppingCart\Exceptions\InvalidModelInstance;

class CartManager implements Contract
{
    /**
     * @var SessionManager $sessionManager
     * @var array $configs
     */
    protected $sessionManager, $configs;

    /**
     * Cart collection
     *
     * @var \Illuminate\Support\Collection
     */
    protected $carts;

    /**
     * Current cart
     *
     * @var Cart
     */
    protected $cart;

    /**
     * Cart count
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
        $this->carts = collect($this->sessionManager->get());
        $this->count = $this->carts->count();
    }

    /**
     * Add new item to cart
     *
     * @param object $model
     * @param int $quantity
     * @return Cart
     * @throws CartAlreadyExists
     */
    public function addItem(object $model, int $quantity = 1): Cart
    {
        if($this->cartExists($model)) {
            throw CartAlreadyExists::create(get_class($model), $model->getKey());
        }

        $cart = $this->createCart($model);

        if($this->cart->quantity != $quantity) {
            $this->cart->setQuantity($quantity);
        }

        $this->storeCarts();
        return $cart;
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
        $this->carts->forget($key);
        $this->storeCarts();
        return true;
    }

    /**
     * Add cart quantity
     *
     * @param object $model
     * @param int $quantity
     * @return Cart $updatedCart
     * @throws InvalidCartQuantity
     */
    public function addQuantity(object $model, int $quantity = 1): Cart
    {
        $this->checkQuantity($model, $quantity);

        $collectionKey = $this->findKey($model);
        $this->cart = $this->carts->pull($collectionKey);
        $currentQuantity = $this->cart->getQuantity();
        $this->cart->setQuantity($currentQuantity + $quantity);
        $updatedCart = $this->cart;
        $this->storeCarts();
        return $updatedCart;
    }

    /**
     * Remove cart quantity
     *
     * @param object $model
     * @param int $quantity
     * @return Cart
     */
    public function removeQuantity(object $model, int $quantity = 1): Cart
    {
        $this->checkQuantity($model, $quantity);

        $collectionKey = $this->findKey($model);
        $cart = $this->carts->pull($collectionKey);
        $currentQuantity = $cart->getQuantity();
        $updatedQuantity = $currentQuantity - $quantity;

        if($updatedQuantity <= 0) {
            $cart->setQuantity(0);
        } else {
            $cart->setQuantity($updatedQuantity);
            $this->cart = $cart;
        }

        $this->storeCarts();
        return $cart;
    }

    /**
     * Check quantity if valid
     *
     * @param $model
     * @param $quantity
     * @throws InvalidCartQuantity
     */
    protected function checkQuantity($model, $quantity)
    {
        if($quantity <= 0) {
            throw InvalidCartQuantity::create(get_class($model), $model->getKey(), $quantity);
        }
    }

    /**
     * Create a new cart
     *
     * @param object $model
     * @return Cart
     */
    protected function createCart(object $model): Cart
    {
        $this->checkModel($model);
        $modelName = get_class($model);
        $cart = new Cart($model->getKey(), $modelName);
        $modelAttributeConfig = $this->configs['model_attributes'];

        if(array_key_exists($modelName, $modelAttributeConfig)) {
            foreach ($modelAttributeConfig[$modelName] as $attribute) {
                $cart->$attribute = $model->$attribute;
            }
        } else {
            foreach ($model->getAttributes() as $attribute => $value) {
                $cart->$attribute = $value;
            }
        }

        $this->setCart($cart);
        return $this->getCart();
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
     * @return Cart
     * @throws CartNotFound
     */
    public function find(object $model): Cart
    {
        $modelName = get_class($model);
        $modelKey = $model->getKey();

        $cart = $this->carts->first(function($cart) use ($modelName, $modelKey) {
            return $cart->model_name == $modelName && $cart->id == $modelKey;
        });

        if( ! $cart) {
            throw CartNotFound::create($modelName, $modelName);
        }
        return $cart;
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
        $modelName = get_class($model);
        $modelKey = $model->getKey();

        $key = $this->carts->search(function ($cart) use ($modelName, $modelKey) {
            return $cart->model_name == $modelName && $cart->id == $modelKey;
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
    public function storeCarts(): void
    {
        if($this->cart) {
            $this->carts->add($this->cart);
            $this->cart = null;
        }

        $this->sessionManager->add($this->carts);
        $this->count = $this->carts->count();
    }

    /**
     * Get current cart object
     *
     * @return Cart
     */
    public function getCart(): Cart
    {
        return $this->cart;
    }

    /**
     * Set current curt object
     *
     * @param Cart $cart
     */
    public function setCart(Cart $cart)
    {
        $this->cart = $cart;
    }

    /**
     * Get carts count
     *
     * @return int
     */
    public function count(): int
    {
        return $this->count;
    }

    /**
     * Get all cart element
     *
     * @return Collection
     */
    public function get(): collection
    {
        return $this->carts;
    }

    /**
     * Check cart already exist
     *
     * @param object $model
     * @return bool
     */
    public function cartExists(object $model): bool
    {
        return $this->carts->contains(function ($cart) use ($model) {
            return $cart->model_name == get_class($model) && $cart->id == $model->getKey();
        });
    }
}