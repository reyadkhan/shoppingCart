<?php

namespace WebApp\ShoppingCart;

class CartItem
{
    protected $id, $model_name, $quantity, $price;
    protected $attributes = [];
    
    /**
     * CartItem constructor.
     *
     * @param int|string $id
     * @param string $model_name
     * @param float $price
     * @param int $quantity
     */
    public function __construct($id, string $model_name, float $price, int $quantity = 1)
    {
        $this->id = $id;
        $this->model_name = $model_name;
        $this->quantity = $quantity;
        $this->price = $price;
    }

    /**
     * Get cart id attribute
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set cart id attribute
     *
     * @param $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * Get cart quantity
     *
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * Set cart quantity
     *
     * @param int $quantity
     */
    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }
    
    /**
     * Get cart price
     *
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }
    
    /**
     * Set cart price
     *
     * @param float $price
     */
    public function setPrice(float $price)
    {
        $this->price = $price;
    }
    
    /**
     * Get total price
     *
     * @return float
     */
    public function getTotal(): float
    {
        return $this->price * $this->quantity;
    }

    /**
     * Get cart attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set cart attribute
     *
     * @param string $key
     * @param mixed $value
     */
    public function setAttribute(string $key, $value): void
    {
        if(property_exists($this, $key)) {
            $this->$key = $value;
        } else {
            $this->attributes[$key] = $value;
        }
    }

    /**
     * Get attribute
     *
     * @param string $key
     * @return mixed|null
     */
    public function __get(string $key)
    {
        if(property_exists($this, $key)) {
            return $this->$key;
        }

        if(isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }
        return null;
    }

    /**
     * Set attribute
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set(string $key, $value): void
    {
        if( ! property_exists($this, $key)) {
            $this->attributes[$key] = $value;
        }
    }
}