<?php namespace ProductFlowbv\Magento2\Model;

use Exception;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Framework\Serialize\SerializerInterface;
use ProductFlowbv\Magento2\Api\ProductFlowApiInterface;

class ProductFlowApi implements ProductFlowApiInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepo;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepo;

    /**
     * @var SerializerInterface
     */
    private $_serializer;

    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @param OrderRepositoryInterface $orderRepo
     * @param CartRepositoryInterface $quoteRepo
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param SerializerInterface $serializer
     */
    public function __construct(OrderRepositoryInterface $orderRepo,
                                CartRepositoryInterface $quoteRepo,
                                QuoteIdMaskFactory $quoteIdMaskFactory,
                                SerializerInterface $serializer)
    {
        $this->orderRepo = $orderRepo;
        $this->quoteRepo = $quoteRepo;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->_serializer = $serializer;
    }

    /**
     *
     * @param string $cartId
     * @param mixed $prices
     * @return mixed $result
     * @throws NoSuchEntityException
     * @api
     */
    public function setQuotePrices($cartId, $prices = null)
    {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        $quote = $this->quoteRepo->get($quoteIdMask->getQuoteId());

        $cartItems = $quote->getAllItems();

        foreach ($cartItems as $item) {

            if (isset($prices['items'][$item->getId()])) {
                $price = $prices['items'][$item->getId()]['price'];

                $item->setCustomPrice($price);
                $item->setOriginalCustomPrice($price);
            }
        }

        $quote->setExtShippingInfo($this->_serializer->serialize($prices['shipping-price']));

        $quote->save();

        return true;
    }

    /**
     *
     * @param int $orderId
     * @param mixed $attributes
     * @return boolean
     * @throws Exception
     * @api
     */
    public function setOrderAttributes($orderId, $attributes = null)
    {
        if (is_null($attributes)) throw new Exception('No attributes found');

        $order = $this->orderRepo->get($orderId);

        $order->setData('productflow_external_identifier', $attributes['productflow_external_identifier']);
        $order->setData('productflow_marketplace_name', $attributes['productflow_marketplace_name']);
        $orderItems = $order->getAllItems();

        $order->save();

        return true;
    }
}