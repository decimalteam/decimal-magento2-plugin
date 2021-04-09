define([
    'Magento_Checkout/js/view/payment/default',
    'jquery',
    'mage/validation'
], function (Component, $) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Decimal_Decimal/payment/decimal-form',
            assistantId: '',
            coin:''
        },

        /** @inheritdoc */
        initObservable: function () {
            this._super()
                .observe(['assistantId','coin']);

            return this;
        },

        /**
         * @return {Object}
         */
        getData: function () {
            return {
                method: this.item.method,
                'additional_data': {
                    'assistant_id': this.assistantId(),
                    'coin': this.coin()
                }
            };
        },

        /**
         * @return {jQuery}
         */
        validate: function () {
            var form = 'form[data-role=decimal-form]';

            return $(form).validation() && $(form).validation('isValid');
        }
    });
});
