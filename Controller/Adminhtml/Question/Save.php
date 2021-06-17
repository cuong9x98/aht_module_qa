<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace AHT\QA\Controller\Adminhtml\Question;

use AHT\QA\Model\Question\ImageUploader;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action\Context;
use AHT\QA\Model\ResourceModel\Question as Resource;
use AHT\QA\Model\Question;
use AHT\QA\Model\QuestionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use AHT\QA\Helper\Email;
use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Store\Model\ScopeInterface;
use AHT\QA\Helper\Data;

/**
 * Save CMS block action.
 */
class Save extends \AHT\QA\Controller\Adminhtml\Block implements HttpPostActionInterface
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var BlockFactory
     */
    private $questionFactory;

    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    public $helperEmail;

    private $imageUploader;

    protected $cacheTypeList;

    public $scopeConfig;

    protected $_cacheTypeList;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param DataPersistorInterface $dataPersistor
     * @param BlockFactory|null $blockFactory
     * @param BlockRepositoryInterface|null $blockRepository ,
     *
     */


    public function __construct(
        Context $context,
        Registry $coreRegistry,
        DataPersistorInterface $dataPersistor,
        QuestionFactory $questionFactory,
        Resource $resource,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        ImageUploader $imageUploader,
        Email $helperEmail,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,

        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->dataPersistor = $dataPersistor;
        $this->questionFactory = $questionFactory;
        $this->resource = $resource;
        $this->imageUploader = $imageUploader;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->helperEmail = $helperEmail;
        $this->scopeConfig = $scopeConfig;
        $this->_cacheTypeList = $cacheTypeList;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            if (empty($data['qa_id'])) {
                $data['qa_id'] = null;
            }

            /** @var \Magento\Cms\Model\Block $model */
            $model = $this->questionFactory->create();

            $id = $this->getRequest()->getParam('qa_id');
            if (isset($data['image'][0]['name'])) {
                $imageName = $data['image'][0]['name'];
            } else {
                $imageName = '';
            }
            if ($id) {
                try {
                    $this->resource->load($model, $id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This block no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }
            $data['image_path'] = $imageName;
            $model->setData($data);

            try {
                $this->resource->save($model);
                //Clean cache
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $cacheManager = $objectManager->get('\Magento\Framework\App\CacheInterface');
                $cacheManager->clean('catalog_product_'.$model->getProductId());
                //End Clean cache
                if(($model->getCheck()==1 && $model->getAnswer()!="")){
                    //-------------------------------------Send email
                    $receiverInfo = [
                        'name' => $model->getName(),
                        'email' => $model->getEmail(),
                        'question' => $model->getQuestion(),
                        'awser' => $model->getAnswer(),
                    ];

                    $store = $this->storeManager->getStore();

                    $templateParams = ['store' => $store, 'administrator_name' => $receiverInfo['name'],'awser' => $receiverInfo['awser']];
                    $template_id = $this->scopeConfig->getValue('question_pending/question/email_template', ScopeInterface::SCOPE_STORE);

                    $transport = $this->transportBuilder->setTemplateIdentifier(
                        $template_id
                    )->setTemplateOptions(
                        ['area' => 'frontend', 'store' => $store->getId()]
                    )->addTo(
                        $receiverInfo['email'], $receiverInfo['name']
                    )->setTemplateVars(
                        $templateParams
                    )->setFrom(
                        'general'
                    )->getTransport();
                    $transport->sendMessage();
                    //---------------------------------------End send email
                }
                $types = array('full_page');
                foreach ($types as $type) {
                    $this->_cacheTypeList->cleanType($type);
                }
                if ($imageName) {
                    $this->imageUploader->moveFileFromTmp($imageName);
                }
                $this->messageManager->addSuccessMessage(__('You saved the block.'));
                $this->dataPersistor->clear('aht_question');
                return $this->processBlockReturn($model, $data, $resultRedirect);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the block.'));
            }

            $this->dataPersistor->set('aht_question', $data);

            return $resultRedirect->setPath('*/*/edit', ['qa_id' => $id]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Process and set the block return
     *
     * @param \Magento\Cms\Model\Block $model
     * @param array $data
     * @param \Magento\Framework\Controller\ResultInterface $resultRedirect
     * @return \Magento\Framework\Controller\ResultInterface
     */
    private function processBlockReturn($model, $data, $resultRedirect)
    {
        $redirect = $data['back'] ?? 'close';

        if ($redirect === 'continue') {
            $resultRedirect->setPath('*/*/edit', ['qa_id' => $model->getId()]);
        } else if ($redirect === 'close') {
            $resultRedirect->setPath('*/*/');
        } else if ($redirect === 'duplicate') {
            $duplicateModel = $this->questionFactory->create(['data' => $data]);
            $duplicateModel->setId(null);
            $duplicateModel->setData('status', 1);
            $this->resource->save($duplicateModel);
            $id = $duplicateModel->getId();
            $this->messageManager->addSuccessMessage(__('You duplicated the block.'));
            $this->dataPersistor->set('aht_question', $data);
            $resultRedirect->setPath('*/*/edit', ['qa_id' => $id]);
        } elseif ($redirect === 'next') {
            $resultRedirect->setPath('*/*/edit', ['qa_id' => $this->getIndex($data['qa_id'])]);
        }
        return $resultRedirect;
    }

    private function getIndex($id)
    {
        $collection = $this->questionFactory->create()->getCollection()->getAllIds();
        $index = array_search($id, $collection);
        $next = $index;
        $question = $this->questionFactory->create();
        if ($index == (count($collection) - 1)) {
            $next = 0;
        } else {
            $next = $index + 1;
        }
        return $question->load($collection[$next])->getId();
    }
}
