<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace AHT\QA\Controller\Customer;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Review\Controller\Customer as CustomerController;
use AHT\QA\Model\QuestionFactory;

class View extends CustomerController
{
    /**
     * @var \AHT\QA\Model\QuestionFactory
     */
    protected $questionFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        QuestionFactory $questionFactory
    ) {
        $this->questionFactory = $questionFactory;
        parent::__construct($context, $customerSession);
    }

    /**
     * Render review details
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $question = $this->questionFactory->create()->load($this->getRequest()->getParam('qa_id'));
        if ($question->getUserId() != $this->customerSession->getCustomerId()) {
            /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
            $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            $resultForward->forward('noroute');
            return $resultForward;
        }
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        if ($navigationBlock = $resultPage->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('qanda/customer');
        }
        $resultPage->getConfig()->getTitle()->set(__('Question Details'));
        return $resultPage;
    }
}
