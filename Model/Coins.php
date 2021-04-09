<?php


namespace Decimal\Decimal\Model;


class Coins implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://devnet-gate.decimalchain.com/api/coin");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $options=json_decode($output,true);
        $coins=$options['result']['coins'];
        $out=[];
        foreach ($coins as $coin){
            $out[] = ['value' => $coin['symbol'], 'label' =>  $coin['symbol']];
        }
        return $out;
    }
}
