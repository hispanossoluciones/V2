<?php
namespace App\Modules\Magento\Services;

use App\Modules\Magento\Helpers\TestApi;
use App\Modules\Magento\Models\Tokens;
use Exception;

class TransService
{
    private $api    = null;

    public function __construct()
    {
        $this->api = new TestApi();
    }

    /**
     *      Traducir a un string
     *    @author Jose Angel Delgado <esojangel@gmail.com>
     */
    public function trans($string)
    {
        return $this->api->trans($string);
    }

    /**
     *    get token
     *    @author Jose Angel Delgado <esojangel@gmail.com>
     */
    public function token()
    {
        return $this->api->token();
    }
    
}
