<?php

namespace Decimal\Decimal\Model;

use Magento\Quote\Api\Data\PaymentInterface;

class Decimal extends \Magento\Payment\Model\Method\AbstractMethod
{
    const PAYMENT_METHOD_DECIMAL_CODE = 'decimal';

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = self::PAYMENT_METHOD_DECIMAL_CODE;

    /**
     * @var string
     */
    protected $_formBlockType = \Decimal\Decimal\Block\Form\Decimal::class;

    /**
     * @var string
     */
    protected $_infoBlockType = \Decimal\Decimal\Block\Info\Decimal::class;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isOffline = true;
}
