<?php namespace ProductFlowbv\Magento2\Model\Carrier;

use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Authorization\Model\UserContextInterface;

use Psr\Log\LoggerInterface;

class ProductFlow extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'productflow';

    /**
     * @var string
     */
    private $_name = 'ProductFlow';

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @var MethodFactory
     */
    private $_userContext;

    /**
     * @var SerializerInterface
     */
    private $_serializer;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param UserContextInterface $userContext
     * @param SerializerInterface $serializer
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        UserContextInterface $userContext,
        SerializerInterface $serializer,
        array $data = []
    )
    {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_logger = $logger;

        $this->_userContext = $userContext;

        $this->_serializer = $serializer;

        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * @param RateRequest $request
     * @return Result|bool
     */
    public function collectRates(RateRequest $request)
    {
        $userType = $this->_userContext->getUserType();
        if ($userType != UserContextInterface::USER_TYPE_INTEGRATION && $userType != UserContextInterface::USER_TYPE_ADMIN) return false;

        $result = $this->_rateResultFactory->create();
        $method = $this->_rateMethodFactory->create();

        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('carrier_title'));
        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('method_title'));

        $quoteItems = $request->getAllItems();
        if (count($quoteItems) == 0) {
            $shippingPrice = 0.00;
        } else {
            $quoteItem = $quoteItems[0];
            $quote = $quoteItem->getQuote();
            $shippingPrice = $this->_serializer->unserialize($quote->getExtShippingInfo());
        }

        $method->setPrice($shippingPrice);
        $method->setCost(0);

        $result->append($method);

        return $result;
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('method_title')];
    }
}
