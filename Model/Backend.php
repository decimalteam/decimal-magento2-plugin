<?php

namespace Decimal\Decimal\Model;

use http\Exception;
use Decimal\Decimal\Api\BackendInterface;

/**
 * @api
 */
class Backend extends \Magento\Framework\Model\AbstractModel implements BackendInterface
{

    /**
     * @var RequestInterface
     */
    protected $request;
    /**
     * api callback endpoint
     *
     * @return mixed
     */

    /**
     * Callback constructor.
     * @param RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\Webapi\Rest\Request $request,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor
    )
    {
        $this->request = $request;
        $this->customerRepository = $customerRepository;
        $this->encryptor = $encryptor;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function postCallback()
    {
        $headers=apache_request_headers()['Authorization'];
        $arr=explode(' ',$headers);
        $decoded=explode(':',base64_decode($arr[1]));
        $passPGW=$decoded[1];

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $sql = "SELECT * FROM `admin_user` WHERE firstname='admin'";
        $result = $connection->fetchAll($sql);
        $pass=$result[0]['password'];
        if($pass==$passPGW){
            $dataPGW=$this->request->getBodyParams();
            $dataExplorer=json_decode($this->getHashInfo($dataPGW['tx_id']));
            if(isset($dataExplorer->ok)){
                $coin=$dataExplorer->result->data->coin;
                $amount=$dataExplorer->result->data->amount/pow(10,18);
                $address=$dataPGW['address'];
                $sql = "SELECT coin,total_paid_coins FROM `decimal_table` WHERE invoice_address='$address'";
                $record = $connection->fetchAll($sql);
                if(isset($record[0])){
                    if(mb_strtolower($record[0]['coin'])==mb_strtolower($coin) && round($record[0]['total_paid_coins'],2)==$amount){
                        $sql = "UPDATE `decimal_table` SET status=1 WHERE invoice_address='$address'";
                        $connection->query($sql);
                    }
                }
            }
        }



    }
    /**
     * @param $hash
     */

    function getHashInfo($hash){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://devnet-explorer-api.decimalchain.com/api/tx/".$hash);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }


}
