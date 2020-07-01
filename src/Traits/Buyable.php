<?php

namespace WebApp\ShoppingCart\Traits;

use WebApp\ShoppingCart\Exceptions\InvalidModelInstance;
use Illuminate\Database\Eloquent\Model;

trait Buyable
{
    /**
     * Get buyable class primary key
     *
     * @return int|string
     * @throws InvalidModelInstance
     */
    public function getKey()
    {
        if($this instanceof Model) {
            return $this->getAttribute($this->primaryKey);
        }

        if(property_exists($this, 'id') && ! empty($this->id)) {
            return $this->id;
        }

        if(property_exists($this, 'primaryKey') && ! empty($this->{$this->primaryKey})) {
            return $this->{$this->primaryKey};
        }

        if(property_exists($this, 'primary_key') && ! empty($this->{$this->primary_key})) {
            return $this->{$this->primary_key};
        }

        throw new InvalidModelInstance("Invalid model instance." . get_class($this) . " Model should have 'id' or 'primaryKey/primary_key' attribute as identifier.");
    }

    /**
     * Get class attributes
     *
     * @return array
     */
    public function getAttributes(): array
    {
        if($this instanceof Model) {

            $this->mergeAttributesFromClassCasts();
            return $this->attributes;
        }

        return get_object_vars($this);
    }
}