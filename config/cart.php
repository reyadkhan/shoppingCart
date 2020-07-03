<?php

return[

    /**
     * -----------------------------------------------------------------------------------------------------------------
     *
     * Shopping cart configuration file. You can change the values as your need.
     * The `session_key` variable contains the session where the cart data will be stored.
     * The `model_attributes` contains the name of the model with the attributes which will be stored in cart session.
     * This variable is optional. If the model name is not present under `model_attributes` variable then, all
     * attributes of the following model that will be provided to cart `addItem` method will be stored in cart session.
     *
     * -----------------------------------------------------------------------------------------------------------------
     */

    'session_key' => 'webAppShoppingCart',

    'model_attributes' => [

        // you can put your model and expected attributes those you want to store in cart session and show in cart page

//        'App\Product' => [
//            'name', 'vendor', 'price', 'image'
//        ]

    ]

];