define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'decimal',
                component: 'Decimal_Decimal/js/view/payment/method-renderer/decimal'
            }
        );
        return Component.extend({});
    }
);
