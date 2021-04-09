<?php

namespace Decimal\Decimal\Block\Info;

class Decimal extends \Magento\Payment\Block\Info
{
    /**
     * @var string
     */
    protected $_template = 'Decimal_Decimal::info/decimal.phtml';

    /**
     * @return string
     */
    public function toPdf()
    {
        $this->setTemplate('Decimal_Decimal::info/pdf/decimal.phtml');
        return $this->toHtml();
    }
}
