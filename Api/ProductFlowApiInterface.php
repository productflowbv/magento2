<?php namespace ProductFlowbv\Magento2\Api;

interface ProductFlowApiInterface
{
    /**
     *
     * @param string $cartId
     * @param mixed $prices
     * @return boolean
     * @api
     */
    public function setQuotePrices($cartId, $prices = null);

    /**
     *
     * @param int $orderId
     * @param mixed $attributes
     * @return boolean
     * @api
     */
    public function setOrderAttributes($orderId, $attributes = null);
}