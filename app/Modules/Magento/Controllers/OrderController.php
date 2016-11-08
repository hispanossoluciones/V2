<?php
namespace App\Modules\Magento\Controllers;

use App\Modules\Magento\Services\OrderService;
use App\Modules\Magento\Services\MeliService;
use V2\Core\Controllers\ControllerCore;

class OrderController extends ControllerCore
{
    private $service = null;  

    public function __construct()
    {
        $this->service = new OrderService();
        $this->meli = new MeliService();        
    } 

    /**
     * Get Token.
     *
     * @param  ###
     *
     * @return $token
     */
    public function token()
    {   
        $user = 'lmcedenho';
        $pass = '04abril1991';

        $token = $this->service->token($user, $pass);

        return $token;
    } 

    /**
     * Get Token with soap.
     *
     * @param  ###
     *
     * @return $token
     */
    public function tokenSoap()
    {   
        // Magento login information 
        $mage_url = 'http://magentov1.dev/api/soap?wsdl'; 
        $mage_user = 'lmcedenho'; 
        $mage_api_key = '04abril1991'; 
        // Initialize the SOAP client 
        $soap = new \SoapClient( $mage_url ); 
        // Login to Magento 
        $session_id = $soap->login( $mage_user, $mage_api_key );

        return $session_id;
    } 

    /**
     * Get Token mercadolibre by yaxa api.
     *
     * @param  ###
     *
     * @return $token
     */
    public function tokenMeli($id = null)
    {         
        $token = $this->meli->token($id);

        return $token;
    } 

    /**
     * Get Product.
     *
     * @param  ###
     *
     * @return $product
     */
    public function getProduct()
    {   
        $token = $this->token();

        $product = $this->service->product($token, 'sku3');

        return $product;
    } 

    /**
     * Create empty car.
     *
     * @param  ###
     *
     * @return $token
     */
    public function createCart($token = null)
    {   
        if($token){
            $cart = $this->service->createCart($token);

            return $cart;
        }
        $token = $this->token();

        $cart = $this->service->createCart($token);

        return $cart;
    }  

    /**
     * Get empty car.
     *    
     *
     * @return $cart
     */
    public function getCart()
    {         
        $token = $this->token();

        $cart = $this->createCart($token);

        $getCart = $this->service->getCart($token, $cart);        

        return $getCart;
    } 

    /**
     * add Product to Cart.
     *
     * @param  ###
     *
     * @return $cart
     */
    public function addProducttoCart($token = null, $cart = null)
    {   
        if($token and $cart){
            $sku = 'sku3';     

            $getCart = $this->service->addProducttoCart($token, $cart, $sku);        

            return $getCart;
        }

        $token = $this->token();

        $cart = $this->createCart($token);  

        $sku = 'sku3';     

        $getCart = $this->service->addProducttoCart($token, $cart, $sku);        

        return $getCart;
    }   
    
    /**
     * Add shipping information
     *
     * @param  ###
     *
     * @return $shipping
     */
    public function addShipping($token = null, $cart = null, $ordersMeli = null)
    {   
        foreach($ordersMeli['results'] as $key=>$val){
            
            $addressInformation = array(
                "shippingAddress" => array(
                    "region" => "MH",
                    "region_id" => 0, 
                    "country_id" => "IN", 
                    "street" => ["Chakala,Kalyan (e)"] ,//string
                    "company" => "abc", 
                    "telephone" => "1111111", 
                    "postcode" => "12223", 
                    "city" => "Mumbai", 
                    "firstname" => "Sameer".$val['id'], //test id Orders
                    "lastname" => "Sawant", 
                    "email" => "paul@qooar.com", 
                    "prefix" => "address_", 
                    "region_code" => "MH", 
                    "sameAsBilling" => 1 
                ), 
                "billingAddress" => array(
                    "region" => "MH", 
                    "region_id" => 0, 
                    "country_id" => "IN", 
                    "street" => ["Chakala,Kalyan (e)"], //string
                    "company" => "abc", 
                    "telephone" => "1111111", 
                    "postcode" => "12223", 
                    "city" => "Mumbai", 
                    "firstname" => "Sameer", 
                    "lastname" => "Sawant", 
                    "email" => "paul@qooar.com",
                    "prefix" => "address_", 
                    "region_code" => "MH" 
                ), 
                "shipping_method_code" => "flatrate", 
                "shipping_carrier_code" => "flatrate" 
            );            
        } 

        echo $addressInformation; 

        /*
        if($token and $cart){

            $addProducttoCart = $this->addProducttoCart($token, $cart);                     

            $shipping = $this->service->addShipping($token, $cart, $addressInformation);        

            return $shipping;

        }

        $token = $this->token();

        $cart = $this->createCart($token);

        $addProducttoCart = $this->addProducttoCart($token, $cart);         

        $shipping = $this->service->addShipping($token, $cart, $addressInformation);        

        return $shipping;
        */
    }   

    /**
     * Get payment methods.
     *    
     *
     * @return $paymentMethods
     */
    public function getPayment()
    {         
        $token = $this->token();

        $cart = $this->createCart($token);

        $addShipping = $this->addShipping($token, $cart);

        $getPayment = $this->service->getPayment($token, $cart);        

        return $getPayment;
    }

    /**
     * Create orders.
     *    
     *
     * @return $order
     */
    public function createOrder()
    {   
        $ordersMeli = $this->getOrders('4830137151559115');
        
        $ordersMeli = json_decode($ordersMeli, true);             
               
        $token = $this->token();

        $cart = $this->createCart($token);

        $addShipping = $this->addShipping($token, $cart, $ordersMeli);

        $getPayment = $this->service->getPayment($token, $cart);    

        $method = array( 
            "method" => "checkmo"       
        );    

        $order = $this->service->createOrder($token, $cart, $method);   

        return $order;        
    } 

    /**
     * Get user data by token.
     *
     * @param ###
     *
     * @return array
     */
    public function getUserMeli($id)
    {   
        $token = $this->tokenMeli($id); //id test 

        $user = $this->meli->getUser($token);

        return array('user' => $user->id, 'token' => $token);
    }

    /**
     * Get orders data.
     *
     * @param ###
     *
     * @return $orders
     */
    public function getOrders($id)
    {   

        //$id = end($url->url_elements);
       
        $data = $this->getUserMeli($id); //id test 4830137151559115
        
        $user = $data['user'];

        $token = $data['token'];        

        $orders = $this->meli->getOrders($user, $token);

        return $orders;        
    }
}
