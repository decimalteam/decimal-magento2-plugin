<?php
namespace Decimal\Decimal\Observer;

use DecimalSDK\Wallet;

class Orderplaceafter implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Webapi\Controller\Rest\InputParamsResolver
     */


    protected $scopeConfig;


    public function __construct(
                                 \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
                                 \Magento\User\Model\UserFactory $userFactory,
                                 \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->userFactory = $userFactory;
        $this->customerRepository = $customerRepository;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        //check decimal method
        $order = $observer->getEvent()->getOrder();
        $order->save();
        $payment = $order->getPayment();
        $method=$payment->getMethod();
        if ($payment->getMethod() != 'decimal') {
            return $this;
        }

        //get all data from order
        $orderId = $order->getIncrementId();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart');
        $totalPrice = $cart->getQuote()->getGrandTotal();
        $quantity=$cart->getQuote()->getItemsCount();
        //convert price to coins
        $currency=$this->getCurrencyCoin('del');
        $total_paid_coins= $totalPrice/$currency;
        //generate invoice
        $mnemonics_phrase=$this->scopeConfig->getValue('payment/decimal/mnemonic_phrase', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $wallet = new Wallet($mnemonics_phrase,"m/44'/60'/0'/0/$orderId");
        $invoice=$wallet->getAddress();

        $_SESSION['invoice']=$invoice;
        $_SESSION['totalPaidCoins']=$total_paid_coins;
        $_SESSION['totalPrice']=$totalPrice;
        $_SESSION['coin']='del';
        $_SESSION['quantity']=$quantity;
        $_SESSION['decimal']=1;

        //send data to PGW
        $this->sendInvoice($invoice,'del');

        //save data in decimal_table
        $this->_resources = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\ResourceConnection');
        $connection= $this->_resources->getConnection();
        $themeTable = $this->_resources->getTableName('decimal_table');
        $sql = "INSERT INTO " . $themeTable . "(order_id,coin,invoice_address,total_paid,total_paid_coins) VALUES ('$orderId','del','$invoice','$totalPrice','$total_paid_coins')";
        $connection->query($sql);
    }

    function  sendInvoice($invoice,$coin){

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('admin_user');
        $sql = "SELECT * FROM `admin_user` WHERE firstname='admin'";
        $result = $connection->fetchAll($sql);

        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $baseUrl=$storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);

        $email=$result[0]['email'];
        $name=$result[0]['username'];
        $pass=$result[0]['password'];
        $url=$baseUrl.'rest/V1/decimal-callback';

        //register shop
        $headers = [
            'Content-Type:application/json',
            'accept:application/json'
        ];
        $shop=[
            "email"=>$email,
            "password"=>$pass,
            "name"=>$name,
            "callbackURL"=>$url
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, 'https://gateway.decimalchain.com/api/register');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($shop));
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT    5.0');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $server_output = curl_exec($ch);
        curl_close ($ch);

        //register invoice
        $headers = [
            'Content-Type:application/json',
            'accept:application/json',
            'Authorization: Basic '. base64_encode("$name:$pass")
        ];
        $params=[
            'address'=>$invoice,
            'currency'=>$coin
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, 'https://gateway.decimalchain.com/api/address/add');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $server_output = curl_exec($ch);
        curl_close ($ch);
        return $server_output;
    }

    public function getCurrencyCoin($coin){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://devnet-explorer-api.decimalchain.com/api/v1/stats");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $options=json_decode($output,true);
        foreach ($options as $key=>$value){
            if(mb_strtolower($coin)==mb_strtolower($key)){
                return $value['price'];
            }
        }
    }
}
