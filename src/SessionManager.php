<?php

namespace WebApp\ShoppingCart;


class SessionManager
{
    /**
     * Session key
     *
     * @var string
     */
    private $key;

    /**
     * SessionManager constructor.
     *
     * @param string $key
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * Store in session
     *
     * @param $sessionValue
     */
    public function add($sessionValue): void
    {
        session([$this->key => $sessionValue]);
    }

    /**
     * Get from session
     *
     * @return mixed
     */
    public function get()
    {
        return session($this->key);
    }

    /**
     * Remove from session
     *
     */
    public function remove(): void
    {
        session()->forget($this->key);
    }

    /**
     * Get session key
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Set session key
     *
     * @param string $key
     * @return $this
     */
    public function setKey(string $key): self
    {
        $this->key = $key;
        return $this;
    }
}