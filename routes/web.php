<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->post('signup',
    [
        'uses' => 'AuthController@signup'
    ]
);

$router->get('signup', function () use ($router) {
    return $router->app->version();
});


$router->post(
    'auth/login',
    [
        'uses' => 'AuthController@authenticate'
    ]
);


$router->group(
    ['middleware' => 'jwt.auth'],
    function() use ($router) {
        /*
         * JWT Auth Protected Routes here
         */


        //customer routes
        $router->post(
            'placeorder',[
                'middleware' => 'check.customer',
                'uses' => 'OrderController@placeOrder'
            ]
        );


        //admin routes
        $router->group(
            ['middleware' => 'check.admin'],
            function() use ($router) {
                /*
                 * JWT Auth Protected Routes here
                 */
                $router->get(
                    'orderlist',[
                        'uses' => 'OrderController@orderList'
                    ]
                );

                $router->post(
                    'assignDeliveryPerson',[
                        'uses' => 'OrderController@assignDeliveryPerson'
                    ]
                );

            }
        );

        //change order status
        $router->post(
            'changeorderstatus',[
                'middleware' => 'check.deliveryperson',
                'uses' => 'OrderController@changeOrderStatus'
            ]
        );


    }
);