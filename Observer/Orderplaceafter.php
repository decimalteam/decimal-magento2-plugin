<?php
namespace Decimal\Decimal\Observer;

use DecimalSDK\Wallet;

class Orderplaceafter implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Webapi\Controller\Rest\InputParamsResolver
     */
    protected $inputParamsResolver;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $requestInterface;


    public function __construct( \Magento\Webapi\Controller\Rest\InputParamsResolver $inputParamsResolver,
                                 \Magento\Framework\App\RequestInterface $requestInterface)
    {
        $this->inputParamsResolver = $inputParamsResolver;
        $this->requestInterface = $requestInterface;
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
        //generate invoice
        $mnemonics_phrase=$this->context->getScopeConfig()->getValue('payment/decimal/mnemonic_phrase', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $wallet = new Wallet($mnemonics_phrase,"m/44'/60'/0'/0/$orderId");
        $invoice=$wallet->getAddress();

        //save data in decimal_table
        $this->_resources = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\ResourceConnection');
        $connection= $this->_resources->getConnection();
        $themeTable = $this->_resources->getTableName('decimal_table');
        $sql = "INSERT INTO " . $themeTable . "(order_id,coin,invoice_address,total_paid,total_paid_coins) VALUES ('$orderId','test','test',$totalPrice,$mnemonics_phrase)";
        $connection->query($sql);
    }
}
