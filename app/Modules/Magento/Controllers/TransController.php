<?php
namespace App\Modules\Magento\Controllers;

use App\Modules\Magento\Services\OrderService;
use V2\Core\Controllers\ControllerCore;

class TransController extends ControllerCore
{
    private $service = null;  
        
    public function token()
    {
        $this->service = new TransService();
        $token = $this->service->token();

        return $token;
    }   
}
