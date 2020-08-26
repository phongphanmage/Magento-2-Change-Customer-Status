<?php
namespace Doublep\CustomerExtent\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\InputException;

/**
 * Class CustomerLogin
 * @package Doublep\CustomerExtent\Observer
 */
class CustomerLogin implements ObserverInterface
{
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    private $customerFactory;

    /**
     * CustomerLogin constructor.
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory
    )
    {
        $this->customerFactory          = $customerFactory;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     * @throws InputException
     */
    public function execute(Observer $observer)
    {
        $customer = $observer->getEvent()->getModel();

        $modelCustomer = $this->customerFactory->create()->load($customer->getEntityId());

        if ( $modelCustomer && !$modelCustomer->getIsActive() ) {
            throw new InputException(__(
                'The account sign-in was incorrect or your account is disabled temporarily. '
                . 'Please wait and try again later.'
            ));
        } else {
            return $this;
        }
    }
}
