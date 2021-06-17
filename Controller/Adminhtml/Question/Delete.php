<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace AHT\QA\Controller\Adminhtml\Question;

use Magento\Framework\App\Action\HttpPostActionInterface;
use AHT\QA\Model\QuestionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use AHT\QA\Model\ResourceModel\Question as Resource;

class Delete extends \Magento\Cms\Controller\Adminhtml\Block implements HttpPostActionInterface
{
    protected $_cacheTypeList;

    private $questionFactory;
    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function __construct(
        QuestionFactory $questionFactory,
        Resource $resource,
        Registry $coreRegistry,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        Context $context
    )
    {
        $this->questionFactory = $questionFactory;
        $this->resource = $resource;
        $this->_cacheTypeList = $cacheTypeList;
        parent::__construct($context, $coreRegistry);
    }

    public function execute()
    {

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('qa_id');
        $model = $this->questionFactory->create();
        $this->resource->load($model, $id);
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create(\AHT\QA\Model\Question::class);
                $model->load($id);
                $model->delete();
                //clean cache
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $cacheManager = $objectManager->get('\Magento\Framework\App\CacheInterface');
                $cacheManager->clean('catalog_product_' . $model->getProductId());


                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the question.'));
                $types = array('full_page');
                foreach ($types as $type) {
                    $this->_cacheTypeList->cleanType($type);
                }
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['qa_id' => $id]);
            }
        }

        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a block to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
