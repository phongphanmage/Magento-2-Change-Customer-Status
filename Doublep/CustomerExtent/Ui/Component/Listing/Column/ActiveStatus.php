<?php
namespace Doublep\CustomerExtent\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

/**
 * Class ActiveStatus
 * @package Doublep\CustomerExtent\Ui\Component\Listing\Column
 */
class ActiveStatus extends Column
{
    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layout;

    /**
     * ActiveStatus constructor.
     * @param \Magento\Framework\View\LayoutFactory $layout
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\LayoutFactory $layout,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->layout = $layout;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (array_key_exists($this->getData('name'), $item)) {
                    $item[$this->getData('name')] =  $this->prepareHtml($item[$this->getData('name')]);
                } else {
                    $item[$this->getData('name')] = $this->prepareHtml(0);
                }
            }
        }
        return $dataSource;
    }

    /**
     * @param $value
     * @return mixed
     */
    private function prepareHtml($value)
    {
        $html = $this->layout
            ->create()
            ->createBlock('Magento\Framework\View\Element\Template')
            ->setValue($value)
            ->setTemplate('Doublep_CustomerExtent::customer/listing/column/status.phtml')
            ->toHtml();

        return $html;
    }

}
