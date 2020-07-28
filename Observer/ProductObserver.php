<?php
namespace ProductFlowbv\Magento2\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ProductObserver implements ObserverInterface
{
    /**
     * ProductObserver constructor.
     */
    public function __construct()
    {

    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getProduct();
        $date = date('Y-m-d H:i:s');
        $attr = 'productflow_updated_at';

        $product->setData($attr, $date);
        $product->setCustomAttribute($attr, $date);
    }
}