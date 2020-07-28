<?php
namespace ProductFlowbv\Magento2\Observer;

use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;

class OrderObserver implements ObserverInterface
{

    /**
     * @var Order
     */
    private $orderFactory;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(Order $orderFactory,
                                OrderRepositoryInterface $orderRepository,
                                ProductRepository $productRepository)
    {
        $this->orderFactory = $orderFactory;
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @param Observer $observer
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $orderId = $order->getId();

        $order = $this->orderRepository->get($orderId);

        $attr = 'productflow_updated_at';

        foreach ($order->getAllItems() as $item) {
            $productid = $item->getProductId();
            $product = $this->productRepository->getById($productid);
            $product->getProduct();
            $date = date('Y-m-d H:i:s');
            $product->setData($attr, $date);
            $product->setCustomAttribute($attr, $date);

            $product->getResource()->saveAttribute($product, $attr);
        }
    }
}