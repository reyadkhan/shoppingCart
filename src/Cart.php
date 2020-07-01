<?php

namespace WebApp\ShoppingCart;


class Cart
{
    protected $id, $model_name, $quantity;
    protected $attributes = [];

    /**
     * Cart constructor.
     *
     * @param int|string $id
     * @param string $model_name
     * @param int $quantity
     */
    public function __construct($id, string $model_name, int $quantity = 1)
    {
        $this->id = $id;
        $this->model_name = $model_name;
        $this->quantity = $quantity;
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            extract($this->attributes)
        ];
    }

    /**
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