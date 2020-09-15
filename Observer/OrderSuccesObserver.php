<?php

namespace ProductFlowbv\Magento2\Observer;

use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;

class OrderSuccesObserver implements ObserverInterface
{

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    protected $logger;
    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param Observer $observer
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $quote = $observer->getQuote();
        $order = $observer->getOrder();
        $productflow_external_identifier = $quote->getData('productflow_external_identifier');

        if ($productflow_external_identifier) {
            // Disable emails
            $order->setCanSendNewEmailFlag(false);
        }

    }
}