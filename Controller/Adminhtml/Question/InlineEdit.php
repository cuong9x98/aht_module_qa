<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace AHT\QA\Controller\Adminhtml\Question;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use AHT\QA\Model\QuestionFactory;
use AHT\QA\Model\ResourceModel\Question;

class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'AHT_QA::question_pending';

    /**
     * @var \Magento\Cms\Api\BlockRepositoryInterface
     */
    protected $questionFactory;
    protected $questionResource;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * @param Context $context
     * @param BlockRepository $blockRepository
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        QuestionFactory $questionFactory,
        Question $questionResource,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->questionFactory = $questionFactory;
        $this->questionResource = $questionResource;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($postItems) as $blockId) {
                    /** @var \Magento\Cms\Model\Block $block */
                    $block = $this->questionFactory->create();
                    $this->questionResource->load($block,$blockId);
                    // if (!$block->getId()) {
                    //     throw new NoSuchEntityException(__('The CMS block with the "%1" ID doesn\'t exist.', $blockId));
                    // }
                    try {
                        $block->setData(array_merge($block->getData(), $postItems[$blockId]));
                        $this->questionResource->save($block);
                    } catch (\Exception $e) {
                        $messages[] = '[Question ID: ' . $block->getId() . '] ' . __($e->getMessage());
                        $error = true;
                    }
                }
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add block title to error message
     *
     * @param BlockInterface $block
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithBlockId(BlockInterface $block, $errorText)
    {
        return '[Block ID: ' . $block->getId() . '] ' . $errorText;
    }
}
