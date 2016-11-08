<?php
namespace App\Modules\Magento\Helpers;

use ErrorHandler;
use V2\Core\Rest\Api;

class TestApi extends Api
{   
    public function token($user, $pass)
    {
        $url = 'http://magento.dev/index.php/rest/V1/integration/admin/token';
     
	    $data = array("username" => $user, "password" => $pass);
	    $data_string = json_encode($data);
	     
	    $headers = array(
	        'Content-Type: application/json',
	        'Content-Length: ' . strlen($data_string)
	    );
	     
	    $ch = curl_init($url);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	    $token = curl_exec($ch);
	     
	    $token = json_decode($token);

	    return $token;	    
    }

    public function getProduct($token, $sku)
    {
        $requestUrl = 'http://magento.dev/index.php/rest/V1/products/'.$sku; //name of product sku local
	    $headers = array("Authorization: Bearer $token");
	     
	    $ch = curl_init($requestUrl);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    $result = curl_exec($ch);
	     
	    $result=  json_decode($result);
	    return $result;
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
        $requestUrl = 'http://magento.dev/index.php/rest/V1/guest-carts/';
	    $headers = array("Authorization: Bearer $token");
	     
	    $ch = curl_init($requestUrl);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    $result = curl_exec($ch);
	     
	    $result=  json_decode($result);
	    return $result;
    }  

    /**
     * get empty car.
     *
     * @param  $token
     *
     * @param  $cart
     *
     * @return $cartId
     */
    public function getCart($token, $cart)
    {          
        $requestUrl = 'http://magento.dev/index.php/rest/V1/guest-carts/'.$cart;
	    $headers = array("Authorization: Bearer $token");
	     
	    $ch = curl_init($requestUrl);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    $result = curl_exec($ch);
	     
	    $result=  json_decode($result);	    
	    $cartId = $result->id;    

	    return $cartId;	    
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
       	$setHaders = array('Content-Type:application/json','Authorization:Bearer '.$token);

	    $cartItem = array( 
	        "quote_id" => $cart, 
	        "sku" => $sku, //name of product sku local
	        "qty" => 1 
	    );

	    $cartItem = json_encode(array('cartItem' => $cartItem));

	    $requestURL = "http://magento.dev/rest/V1/guest-carts/".$cart."/items";


	    $ch = curl_init();      
	    curl_setopt($ch,CURLOPT_URL, $requestURL);

	    curl_setopt($ch,CURLOPT_POSTFIELDS, $cartItem);

	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $setHaders);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    if(curl_exec($ch)===false){
	        echo "Curl error: " . curl_error($ch)."\n";
	    }else{
	        $response = curl_exec($ch) ?: "";
	    }
	    curl_close($ch);

	    return $response;
	}  

	/**
     * Add shipping information
     *
     * @param  $token
     *
     * @param  $cart 
     *
     * @param  $addressInformation
     *
     * @return $shipping
     */
    public function addShipping($token, $cart, $addressInformation)
    {   
        $setHaders = array('Content-Type:application/json','Authorization:Bearer '.$token);	    

	    $addressInformation = json_encode(array('addressInformation' => $addressInformation));

	    $requestURL = "http://magento.dev/rest/V1/guest-carts/".$cart."/shipping-information";


	    $ch = curl_init();      
	    curl_setopt($ch,CURLOPT_URL, $requestURL);

	    curl_setopt($ch,CURLOPT_POSTFIELDS, $addressInformation);

	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $setHaders);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    if(curl_exec($ch)===false){
	        echo "Curl error: " . curl_error($ch)."\n";
	    }else{
	        $response = curl_exec($ch) ?: "";
	    }
	    curl_close($ch);

	    return $response;
    }

    /**
     * Get payment methods.
     *    
     *
     * @return $paymentMethods
     */
    public function getPayment($token, $cart)
    {         
        $requestUrl = 'http://magento.dev/index.php/rest/V1/guest-carts/'.$cart.'/payment-information';
	    $headers = array("Authorization: Bearer $token");
	     
	    $ch = curl_init($requestUrl);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    $result = curl_exec($ch);
	     
	    $result=  json_decode($result);

	    return $result;
    } 

    /**
     * Create orders.
     *    
     *
     * @return $order
     */
    public function createOrder($token, $cart, $method)
    {   
    	$setHaders = array('Content-Type:application/json','Authorization:Bearer '.$token);
    	     
        $method = json_encode(array('paymentMethod' => $method));

	    $requestURL = "http://magento.dev/index.php/rest/V1/guest-carts/".$cart."/order";

	    $ch = curl_init();      
	    curl_setopt($ch,CURLOPT_URL, $requestURL);

	    curl_setopt($ch,CURLOPT_POSTFIELDS, $method);

	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $setHaders);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    if(curl_exec($ch)===false){
	        echo "Curl error: " . curl_error($ch)."\n";
	    }else{
	        $response = curl_exec($ch) ?: "";
	    }
	    curl_close($ch);

	    return $response;
    }
}
