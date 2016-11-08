<?php
namespace App\Modules\Magento1\Controllers;

use App\Modules\Magento1\Services\OrderService;
use App\Modules\Magento1\Services\MeliService;
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
     * create Orders
     *
     * @param $url
     *
     * @return $order
     */
    public function createOrders($url)
    {   
        $id = end($url->url_elements);
        $ordersMeli = $this->getOrders($id); //'4830137151559115'        
        $ordersMeli = json_decode($ordersMeli, true);       

        $createOrders = $this->service->createOrders($id, $ordersMeli);       
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
    public function getOrders($id = null)
    {   
        //$id = end($url->url_elements);
       
        $data = $this->getUserMeli($id); //id test 4830137151559115
        
        $user = $data['user'];

        $token = $data['token'];        

        $orders = $this->meli->getOrders($user, $token);

        return $orders;        
    }
}
