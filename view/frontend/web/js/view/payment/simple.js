define(
    [
        'uiComponent',
        'Decimal_Checkout/js/model/payment/renderer-list'
    ],
    function (Component,
              rendererList) {
        'use strict';
        rendererList.push(
            {
                type: 'simple',
                component: 'Decimal_Payment/js/view/payment/method-renderer/simple-method'
            }
        );
        return Component.extend({});
    }
);