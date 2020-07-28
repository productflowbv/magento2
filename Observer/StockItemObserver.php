<?php
namespace ProductFlowbv\Magento2\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Exception\NoSuchEntityException;

class StockItemObserver implements ObserverInterface
{

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * StockItemObserver constructor.
     * @param ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param Observer $observer
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $stockItem = $observer->getItem();
        $productId = $stockItem->getProductId();
        $product = $this->productRepository->getById($productId);
        $date = date('Y-m-d H:i:s');
        $attr = 'productflow_updated_at';

        $product->setData($attr, $date);
        $product->setCustomAttribute($attr, $date);

        $product->getResource()->saveAttribute($product, $attr);
    }
}