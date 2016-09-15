<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace OnTap\MasterCard\Gateway\Config\Hpf;

use OnTap\MasterCard\Model\Ui\Hpf\ConfigProvider;

class Config extends \OnTap\MasterCard\Gateway\Config\Config
{
    const COMPONENT_URI = '%sform/version/%s/merchant/%s/session.js';

    /**
     * @return \Magento\Payment\Model\MethodInterface
     */
    protected function getVaultPayment()
    {
        return $this->getPaymentDataHelper()->getMethodInstance(ConfigProvider::CC_VAULT_CODE);
    }

    /**
     * @return string
     */
    public function getComponentUrl()
    {
        return sprintf(
            static::COMPONENT_URI,
            $this->getApiAreaUrl(),
            $this->getValue('api_version'),
            $this->getMerchantId()
        );
    }
}