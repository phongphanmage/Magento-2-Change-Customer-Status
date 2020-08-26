<?php
namespace Doublep\CustomerExtent\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\App\ResourceConnection;

/**
 * Class MassSubscribe
 */
class MassStatus extends \Magento\Customer\Controller\Adminhtml\Index\AbstractMassAction
    implements HttpPostActionInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var IndexerRegistry
     */
    protected $indexerRegistry;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * MassStatus constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerFactory $customerFactory
     * @param IndexerRegistry $indexerRegistry
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        CustomerRepositoryInterface $customerRepository,
        CustomerFactory $customerFactory,
        IndexerRegistry $indexerRegistry,
        ResourceConnection $resourceConnection
    ) {
        parent::__construct($context, $filter, $collectionFactory);
        $this->customerRepository = $customerRepository;
        $this->customerFactory    = $customerFactory;
        $this->indexerRegistry    = $indexerRegistry;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    protected function massAction(AbstractCollection $collection)
    {
        $status = $this->getRequest()->getParam('status');

        try {
            $connection = $this->resourceConnection->getConnection();

            $connection->update(
                $connection->getTableName('customer_entity'),
                ['is_active' => $status ],
                ['entity_id IN (?)' => $collection->getAllIds()]

            );

            $this->messageManager->addSuccessMessage(__('Data were updated. The data on the grid will be updated once indexing done.'));

            $this->indexerRegistry->get(\Magento\Customer\Model\Customer::CUSTOMER_GRID_INDEXER_ID)->invalidate();
        }catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $resultRedirect->setPath($this->getComponentRefererUrl());

        return $resultRedirect;
    }
}
