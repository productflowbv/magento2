<?php namespace ProductFlowbv\Magento2\Model\Payment;

use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Authorization\Model\UserContextInterface;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Payment\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Payment\Model\Method\Logger;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;

/**
 * Pay In Store payment method model
 */
class ProductFlow extends AbstractMethod
{
    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'productflow';

    /**
     * @var string
     */
    private $_name = 'ProductFlow';

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isOffline = true;

    /**
     * Hides the method from the checkout process
     *
     * @var bool
     */
    protected $_canUseCheckout = true;

    /**
     * @var MethodFactory
     */
    private $_userContext;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param Data $paymentData
     * @param ScopeConfigInterface $scopeConfig
     * @param Logger $logger
     * @param UserContextInterface $userContext
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        Data $paymentData,
        ScopeConfigInterface $scopeConfig,
        Logger $logger,
        UserContextInterface $userContext,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data
        );
        $this->_userContext = $userContext;
    }

    /**
     * Make sure the method is always active, and not dependent on settings
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isActive($storeId = null)
    {
        $userType = $this->_userContext->getUserType();
        return ($userType == UserContextInterface::USER_TYPE_INTEGRATION || $userType == UserContextInterface::USER_TYPE_ADMIN);
    }
}