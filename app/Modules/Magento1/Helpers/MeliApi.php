<?php
namespace App\Modules\Magento1\Helpers;

use ErrorHandler;
use V2\Core\Rest\Api;

class MeliApi extends Api
{   
    /**
     * Get token Mercadolibre.
     *        
     * @param $id
     *
     * @return $token
     */
    public function token($id)
    {      
        $url = 'http://yaxaws.com/MercadoLibre/API/authorize/token/'.$id;     
	     
	    $headers = array(
	        'Content-Type: application/json'	       
	    );
	     
	    $ch = curl_init($url);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	    $token = curl_exec($ch);

	    $token = json_decode($token);

	    $token = $token->data->token;

	    return $token;	
    } 

    /**
     * Get user data by token.
     *
     * @param $token
     *
     * @return $user
     */
    public function getUser($token)
    {          
        $url = 'https://api.mercadolibre.com/users/me?access_token='.$token;     
      	    
	    $headers = array(
	        'Content-Type: application/json'	     
	    );
	     
	    $ch = curl_init($url);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	    $result = curl_exec($ch);	     
	    $result = json_decode($result);
	    curl_close ($ch);

	    return $result;
    } 

    /**
     * Get orders data.
     *
     * @param $user
     *
     * @return $token
     *
     * @return $orders
     */
    public function getOrders($user, $token)
    {   
       	$url = 'https://api.mercadolibre.com/orders/search/recent?seller='.$user.'&access_token='.$token;     
      	    
	    $headers = array(
	        'Content-Type: application/json'	     
	    );
	     
	    $ch = curl_init($url);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	    $result = curl_exec($ch);	  
	    curl_close ($ch);

	    return $result;
    }  
}
