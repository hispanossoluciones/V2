<?php
namespace App\Modules\Magento1\Services;

use App\Modules\Magento1\Helpers\MeliApi;
use App\Modules\Magento1\Models\Tokens;
use Exception;

class MeliService
{
    private $api    = null;

    public function __construct()
    {
        $this->api = new MeliApi();
    }    

    /**
     *    get token
     *
     *    @param $id
     *
     *    @author HispanoSoluciones, C.A
     */
    public function token($id)
    {
        return $this->api->token($id);
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
        $user = $this->api->getUser($token);

        return $user;
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
        $orders = $this->api->getOrders($user, $token);

        return $orders;
    }
}
