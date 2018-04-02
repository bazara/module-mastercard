<?php
/**
 * Copyright (c) 2016. On Tap Networks Limited.
 */

namespace OnTap\MasterCard\Gateway\Request\Hosted;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Sales\Model\Order\Payment;
use Magento\Payment\Gateway\Data\Quote\QuoteAdapter;
use Magento\Checkout\Model\CartFactory;
use OnTap\MasterCard\Gateway\Config\ConfigFactory;

class OrderDataBuilder implements BuilderInterface
{
    /**
     * @var ConfigFactory
     */
    protected $configFactory;

    /**
     * @var CartFactory
     */
    protected $cartFactory;

    /**
     * OrderDataBuilder constructor.
     * @param CartFactory $cartFactory
     * @param ConfigFactory $configFactory
     */
    public function __construct(CartFactory $cartFactory, ConfigFactory $configFactory)
    {
        $this->cartFactory = $cartFactory;
        $this->configFactory = $configFactory;
    }

    /**
     * @return array
     */
    protected function getItemData()
    {
        /** @var \Magento\Checkout\Model\Cart $cart */
        $cart = $this->cartFactory->create();

        $data = [];

        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($cart->getItems() as $item) {
            if ($item->getParentItemId() !== null) {
                continue;
            }
            $data[] = [
                'name' => $item->getName(),
                'description' => $item->getDescription(),
                'sku' => $item->getSku(),
                'unitPrice' => sprintf('%.2F', $item->getRowTotal() - $item->getTotalDiscountAmount()),
                'quantity' => 1,
                //'unitTaxAmount' => 0,
            ];
        }

        return $data;
    }

    /**
     * Builds ENV request
     *
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        $paymentDO = SubjectReader::readPayment($buildSubject);

        /* @var QuoteAdapter $order */
        $order = $paymentDO->getOrder();

        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $payment->getQuote();

        $quote->setReservedOrderId(null);
        $quote->reserveOrderId();

        $quote->collectTotals();

        $config = $this->configFactory->create();
        $config->setMethodCode($payment->getMethod());

        $shipping = $quote->getShippingAddress();

        return [
            'order' => [
                'amount' => sprintf('%.2F', $quote->getGrandTotal()),
                'currency' => $order->getCurrencyCode(),
                'id' => $order->getOrderIncrementId(),
                'item' => $this->getItemData(),
                'shippingAndHandlingAmount' => $shipping->getShippingAmount(),
                'taxAmount' => $quote->getShippingAddress()->getTaxAmount(), // @todo: Virtual goods have no shipping
                'notificationUrl' => $config->getWebhookNotificationUrl(),
            ]
        ];
    }
}
