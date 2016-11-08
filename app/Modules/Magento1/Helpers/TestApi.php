<?php
namespace App\Modules\Magento1\Helpers;

use ErrorHandler;
use V2\Core\Rest\Api;

class TestApi extends Api
{  
    private $service = null;  

    public function __construct()
    {        
        $config=array();
        $this->hostname = "magentov1.dev";
        $this->login = "testApi";
        $this->password = "testApi";
        $this->proxy = new \SoapClient('http://'.$this->hostname.'/api/soap/?wsdl', array('trace'=>1));      
    }     

    /**
     * Get Token with soap.
     *
     * @param  ###
     *
     * @return $token
     */
    public function token()
    {         
        $sessionId = $this->proxy->login($this->login, $this->password);
        return $sessionId;
    }

    /**
     * add Product to cart.
     *
     * @param  $productSku
     *
     * @return $shoppingCartIncrementId
     */
    public function addProducttocart($productSku = null)
    {   
        $sessionId = $this->token();
        $shoppingCartIncrementId = $this->proxy->call( $sessionId, 'cart.create',array( 1 ));
        $arrProducts = array(
            array(
                "product_id" => $productSku[0]['product_id'],
                "quantity" => 1
            )
        );
        $resultCartProductAdd = $this->proxy->call(
            $sessionId,
            "cart_product.add",
            array(
              $shoppingCartIncrementId,
              $arrProducts
            )
        );
        //echo "\nAdding to Cart...\n";
        if ($resultCartProductAdd) {
            //echo "Products added to cart. Cart with id:".$shoppingCartIncrementId; 
        } else {
            //echo "Products not added to cart"; 
        }
        //echo "\n";

        return array('shoppingCartIncrementId' => $shoppingCartIncrementId, 'sessionId' => $sessionId);
    }

    /**
     * create new product.
     *
     * @param  $order_item
     *
     * @param  $sessionId
     *
     * @return $products
     */    
    public function createProduct($order_item = null, $sessionId = null)
    {   

        //echo "id: ".$order_item['item']['id']."\n nombre: ".$order_item['item']['title']."\n precio: ".$order_item['unit_price'];
        
        $attributeSets = $this->proxy->call($sessionId, 'product_attribute_set.list');
        $attributeSet = current($attributeSets); 

        $filters = array(
            'sku' => array('like'=>$order_item['item']['id'])
        );

        $products = $this->proxy->call($sessionId, 'product.list', array($filters));
        if(sizeof($products)){
            return $products;
        } else {
            $this->proxy->call($sessionId, 'catalog_product.create', array(
            'simple', $attributeSet['set_id'], $order_item['item']['id'], array(
            'categories' => array(2),
            'websites' => array(1),
            'name' => $order_item['item']['title'],
            'description' => 'Product description',
            'short_description' => 'Product short description',
            'weight' => '10',
            'status' => '1',
            'url_key' => 'product-url-key',
            'url_path' => 'product-url-path',
            'visibility' => '4',
            'price' => $order_item['unit_price'],
            'tax_class_id' => 1,
            'meta_title' => 'Product meta title',
            'meta_keyword' => 'Product meta keyword',
            'meta_description' => 'Product meta description'
            )));

            $filters = array(
                'sku' => array('like'=>$order_item['item']['id'])
            );
            $products = $this->proxy->call($sessionId, 'product.list', array($filters));
            return $products;
        }
    }

    /**
     * set customer.
     *
     * @param  $order
     *
     * @param  $productSku
     *
     * @return array
     */
    public function setCustomer($order = null, $productSku = null)
    {
        
        $data = $this->addProducttocart($productSku);

        $shoppingCartId = $data['shoppingCartIncrementId'];
        $sessionId = $data['sessionId'];
        
        $customer = array(
            "firstname" => $order['seller']['first_name'],
            "lastname" => $order['seller']['last_name'],    
            "prefix" => $order['id'],  //save id_order ML
            "website_id" => "1",
            "group_id" => "1",
            "store_id" => "1",
            "email" => $order['seller']['email'],
            "mode" => "guest",
        );

        //echo "\nSetting Customer...";
        $resultCustomerSet = $this->proxy->call($sessionId, 'cart_customer.set', array( $shoppingCartId, $customer) );
        if ($resultCustomerSet === TRUE) {
            //echo "\nOK Customer is set";    
        } else {
            //echo "\nOK Customer is NOT set";    
        }

        // Set customer addresses, for example guest's addresses
        $arrAddresses = array(
            array(
                "mode" => "shipping",
                "firstname" => $order['buyer']['first_name'],
                "lastname" => $order['buyer']['last_name'],
                "company" => "testCompany",
                "street" => "testStreet",
                "city" => "Treviso",
                "region" => "TV",
                "postcode" => "31056",
                "country_id" => "IT",
                "telephone" => $order['buyer']['phone']['number'],
                "fax" => $order['buyer']['phone']['number'],
                "is_default_shipping" => 0,
                "is_default_billing" => 0
            ),
            array(
                "mode" => "billing",
                "firstname" => $order['buyer']['first_name'],
                "lastname" => $order['buyer']['last_name'],
                "company" => "testCompany",
                "street" => "testStreet",
                "city" => "Treviso",
                "region" => "TV",
                "postcode" => "31056",
                "country_id" => "IT",
                "telephone" => $order['buyer']['phone']['number'],
                "fax" => $order['buyer']['phone']['number'],
                "is_default_shipping" => 0,
                "is_default_billing" => 0
            )
        );
        //echo "\nSetting addresses...";
        $resultCustomerAddresses = $this->proxy->call($sessionId, "cart_customer.addresses", array($shoppingCartId, $arrAddresses));
        if ($resultCustomerAddresses === TRUE) {
            //echo "\nOK address is set\n"; 
        } else {
            //echo "\nKO address is not set\n"; 
        }

        return array('shoppingCartId' => $shoppingCartId, 'sessionId' => $sessionId);       
    }

    /**
     * get list of shipping methods
     *
     * @param  $order
     *
     * @param  $productSku
     *
     * @return array
     */
    public function getShippingMethods($order = null, $productSku = null)
    {
        $data = $this->setCustomer($order, $productSku);
        $sessionId = $data['sessionId'];
        $shoppingCartId = $data['shoppingCartId'];

        // get list of shipping methods
        $resultShippingMethods = $this->proxy->call($sessionId, "cart_shipping.list", array($shoppingCartId));
        //print_r( $resultShippingMethods );

        return array('shoppingCartId' => $shoppingCartId, 'sessionId' => $sessionId, 'resultShippingMethods' => $resultShippingMethods);      
    }

    /**
     * set shipping methods
     *
     * @param  $order
     *
     * @param  $productSku
     *
     * @return array
     */
    public function setShippingMethods($order = null, $productSku = null)
    {    
        $data = $this->getShippingMethods($order, $productSku);
        $sessionId = $data['sessionId'];
        $shoppingCartId = $data['shoppingCartId'];
        $resultShippingMethods = $data['resultShippingMethods'];

        $randShippingMethodIndex = rand(0, count($resultShippingMethods)-1 );
        $shippingMethod = $resultShippingMethods[$randShippingMethodIndex]["code"];
        //echo "\nShipping method:".$shippingMethod; 
        $resultShippingMethod = $this->proxy->call($sessionId, "cart_shipping.method", array($shoppingCartId, 'flatrate_flatrate'));
        //echo "\nI will check total...\n";
        $resultTotalOrder = $this->proxy->call($sessionId,'cart.totals',array($shoppingCartId));
        //print_r($resultTotalOrder);
        //echo "\nThe products are...\n";
        $resultProductOrder = $this->proxy->call($sessionId,'cart_product.list',array($shoppingCartId));
        //print_r($resultProductOrder);

        return array('shoppingCartId' => $shoppingCartId, 'sessionId' => $sessionId);     
    }

    /**
     * get list of payment methods
     *
     * @param  $order
     *
     * @param  $productSku
     *
     * @return array
     */
    public function getPaymentMethods($order = null, $productSku = null)
    {        
        $data = $this->setShippingMethods($order, $productSku);
        $sessionId = $data['sessionId'];
        $shoppingCartId = $data['shoppingCartId'];

        //echo "\nList of payment methods...";
        $resultPaymentMethods = $this->proxy->call($sessionId, "cart_payment.list", array($shoppingCartId));
        //print_r($resultPaymentMethods);

        return array('shoppingCartId' => $shoppingCartId, 'sessionId' => $sessionId);    
    }

    /**
     * set payment methods
     *
     * @param  $order
     *
     * @param  $productSku
     *
     * @return array
     */
    public function setPaymentMethod( $order = null, $productSku = null )
    {          
        $data = $this->getPaymentMethods($order, $productSku);
        $sessionId = $data['sessionId'];
        $shoppingCartId = $data['shoppingCartId'];

        $paymentMethodString= "checkmo";
        //echo "\nPayment method $paymentMethodString.";
        $paymentMethod = array(
            "method" => $paymentMethodString
        );
        $resultPaymentMethod = $this->proxy->call($sessionId, "cart_payment.method", array($shoppingCartId, $paymentMethod));

        // get full information about shopping cart
        //echo "\nCart info:\n";
        $shoppingCartInfo = $this->proxy->call($sessionId, "cart.info", array($shoppingCartId));
        //print_r( $shoppingCartInfo );
        $licenseForOrderCreation = null;

        return array('shoppingCartId' => $shoppingCartId, 'sessionId' => $sessionId, 'licenseForOrderCreation' => $licenseForOrderCreation);    
    }

    /**
     * create Orders
     *
     * @param $id
     *
     * @param $ordersMeli
     *
     * @return $order
     */
    public function createOrders($id, $ordersMeli)
    { 
        $i=0;
        foreach($ordersMeli['results'] as $key=>$val){                        
            if($i<10){ //test
            foreach($val['order_items'] as $key2=>$val2){
                $sessionId = $this->token();
                $productSku = $this->createProduct($val2, $sessionId);               

                if($productSku){

                    $result = $this->proxy->call($sessionId, 'order.list', array('filter' => array('customer_prefix' => $val['id'])));                   
                    
                    if(sizeof($result)){                  
                        echo "\nnot saved, the order exists";
                    }else{
                    
                    $data = $this->setPaymentMethod($val, $productSku);
                    $sessionId = $data['sessionId'];
                    $shoppingCartId = $data['shoppingCartId'];
                    $licenseForOrderCreation = $data['licenseForOrderCreation'];
                    
                        echo "\nI will create the order: ";
                        $resultOrderCreation = $this->proxy->call($sessionId,"cart.order",array($shoppingCartId, null, $licenseForOrderCreation));                        
                        $this->proxy->call($sessionId, 'sales_order.addComment', array('orderIncrementId' => $resultOrderCreation, 'status' => 'Complete', 'comment' => 'Id Mercadolibre: '.$val['id'], 'notify' => null));
                        echo 'the order has been created: '.$resultOrderCreation; 
                    }
                }
            }
            $i++;  
            }             
        }
    } 
}
