<?php
namespace App\Modules\Magento\Services;

use App\Modules\Magento\Helpers\TestApi;
use App\Modules\Magento\Models\Tokens;
use Exception;

class OrderService
{
    private $api    = null;

    public function __construct()
    {
        $this->api = new TestApi();
    }    

    /**
     *    get token
     *
     *    @author HispanoSoluciones, C.A
     */
    public function token($user, $pass)
    {
        return $this->api->token($user, $pass);
    }

    /**
     *    get product
     *
     *    @param $token
     *    @param $sku
     *
     */
    public function product($token, $sku)
    {
        return $this->api->getProduct($token, $sku);
    }

    /**
     * Create empty car.
     *
     * @param  ###
     *
     * @return $emptyCar
     */
    public function createCart($token)
    {          
        $cart = $this->api->createCart($token);

        return $cart;
    } 

    /**
     * get empty car.
     *
     * @param  $token
     *
     * @param  $cart
     *
     * @return $emptyCar
     */
    public function getCart($token, $cart)
    {          
        $cart = $this->api->getCart($token, $cart);

        return $cart;
    }  

    /**
     * add Product to Cart.
     *
     * @param  ###
     *
     * @return $cart
     */
    public function addProducttoCart($token, $cart, $sku)
    {   
        $cart = $this->api->addProducttoCart($token, $cart, $sku);

        return $cart;
    }  

    /**
     * Add shipping information
     *
     * @param  $token
     *
     * @param  $cart
     *
     * @param  $cart     
     *
     * @return $addressInformation
     */
    public function addShipping($token, $cart, $addressInformation)
    {         
        $shipping = $this->api->addShipping($token, $cart, $addressInformation);

        return $shipping;
    }

    /**
     * Get payment methods.
     *    
     *
     * @return $paymentMethods
     */
    public function getPayment($token, $cart)
    {         
        $payment = $this->api->getPayment($token, $cart);

        return $payment;
    } 

    /**
     * Create orders.
     *    
     *
     * @return $order
     */
    public function createOrder($token, $cart, $method)
    {         
        $order = $this->api->createOrder($token, $cart, $method);

        return $order;
    } 
    
}
