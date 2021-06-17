<?php
namespace AHT\QA\Controller\Adminhtml\Question;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('AHT_QA::question_pending');
        // $resultPage->addBreadcrumb(__('Question pending'), __('Question Pending'));
        // $resultPage->addBreadcrumb(__('Manage Question Pending'), __('Manage Question Pending'));
        $resultPage->getConfig()->getTitle()->prepend(__('Questions'));
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('AHT_QA::question_pending');
    }
}