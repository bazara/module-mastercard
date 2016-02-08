<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace OnTap\Tns\Model\Adminhtml\Source;

use Magento\Framework\Option\ArrayInterface;

class ValidatorBehaviour implements ArrayInterface
{
    const ACCEPT = 'ACCEPT';
    const REJECT = 'REJECT';
    const FRAUD = 'FRAUD';

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => static::ACCEPT,
                'label' => __('Accept')
            ],
            [
                'value' => static::REJECT,
                'label' => __('Reject')
            ],
            [
                'value' => static::FRAUD,
                'label' => __('Suspected fraud')
            ]
        ];
    }
}