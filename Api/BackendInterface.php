<?php

namespace Decimal\Decimal\Api;


use Magento\Framework\Webapi\Rest\Response\Renderer\Json;

interface BackendInterface
{


    /**
     * @return mixed
     * @api
     */
    public function postCallback();


}
