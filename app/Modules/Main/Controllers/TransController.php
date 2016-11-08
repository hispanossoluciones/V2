<?php
namespace App\Modules\Main\Controllers;

use App\Modules\Main\Services\TransService;
use V2\Core\Controllers\ControllerCore;

class TransController extends ControllerCore
{
    private $service = null;
    
    // Middleware before any methods
    protected function before($request) {}
    
    public function index($request)
    {
        $this->service = new TransService();
        return $this->service->trans("Hello Yaxa");
    }

    public function magento()
    {
        $url = 'https://v2.dev/index.php/rest/V1/integration/admin/token';
 
		$data = array("username" => "", "password" => "");
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
		echo $token;
	}
   
}
