<?php
namespace App\Modules\Magento1\Services;

use App\Modules\Magento1\Helpers\TestApi;
use App\Modules\Magento1\Models\Tokens;
use Exception;

class OrderService
{
    private $api    = null;

    public function __construct()
    {
        $this->api = new TestApi();
    }    
  
    /**
     * create Orders
     *
     * @param $id
     *
     * @param $ordersMeli
     *
     * @return $orders
     */
    public function createOrders($id, $ordersMeli)
    {   
         $this->api->createOrders($id, $ordersMeli);
    }    
}
