# RocketFuel - RocketFuel Payment Method for Magento 2
RocketFuel Payment Method for Magento 2

The registration procedure is described in the [documentation RocketFuel](https://dev.rocketdemo.net/help)

# RocketFuel integration.

##Installation

    composer require rocketfuel/module-rocketfuel-magento2

    bin/magento module:enable Rocketfuel_Rocketfuel

    php bin/magento setup:upgrade

##After it:
- go to  https://<your_shop_url>>/<your_admin_path>/admin/system_config/
- goto sales -> payment methods
- paste your merchant id
- set iframe
- callback url should be stored in your rocketfuel merchant settings
