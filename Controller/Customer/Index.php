<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace AHT\QA\Controller\Customer;

use Magento\Review\Controller\Customer as CustomerController;
use Magento\Framework\Controller\ResultFactory;

class Index extends CustomerController
{
    /**
     * Render my product reviews
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        if ($navigationBlock = $resultPage->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('qanda/customer');
        }
        // if ($block = $resultPage->getLayout()->getBlock('question_customer_list')) {
        //     $block->setRefererUrl($this->_redirect->getRefererUrl());
        // }
        $resultPage->getConfig()->getTitle()->set(__('My recent Questions'));
        return $resultPage;
    }
}
