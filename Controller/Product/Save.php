<?php
namespace AHT\QA\Controller\Product;

class Save extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;

	protected $_questionFactory;

	protected $_questionResource;

	protected $_coreRegistry;

	protected $resultRedirect;

	protected $urlInterface;

	private $_cacheTypeList;
	private $_cacheFrontendPool;

	protected $_redirect;
	protected $_customerSession;
	protected $_storeManager;
	protected $messageManager;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
		\AHT\QA\Model\QuestionFactory $questionFactory,
		\AHT\QA\Model\ResourceModel\Question $questionResource,
		\Magento\Framework\Registry $coreRegistry,
		\Magento\Framework\Controller\ResultFactory $result,
		\Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
		\Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
		\Magento\Framework\App\Response\RedirectInterface $resultRedirect,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Store\Model\StoreManagerInterface $storeManager
		)
	{
		$this->_customerSession = $customerSession;
		$this->_storeManager = $storeManager;
		$this->_pageFactory = $pageFactory;
		$this->_questionFactory = $questionFactory;
		$this->_questionResource = $questionResource;
		$this->_coreRegistry = $coreRegistry;
		$this->resultRedirect = $result;
		$this->_cacheTypeList = $cacheTypeList;
		$this->_cacheFrontendPool = $cacheFrontendPool;
		$this->_redirect = $resultRedirect;
		$this->messageManager = $context->getMessageManager();
		return parent::__construct($context);
	}

	public function execute()
	{

		$question = $this->_questionFactory->create();
		try {
			if (isset($_POST['createbtn'])) {
				$question->setName($this->getRequest()->getParam("name"));
				$question->setEmail($this->getRequest()->getParam("email"));
				$question->setQuestion($this->getRequest()->getParam("question"));
				$question->setProductId($this->getRequest()->getParam("productid"));
				$question->setStoreId($this->_storeManager->getStore()->getId());
				$question->setCreatedAt(date('Y-m-d H:i:s'));
				$question->setUpdatedAt(date('Y-m-d H:i:s'));
                $question->setCheck($this->getRequest()->getParam("check"));
			}
			$this->_questionResource->save($question);
			$this->messageManager->addSuccessMessage("You submitted your question for moderation");
		} catch (\Exception $e) {

			$this->messageManager->addErrorMessage("We can\'t post your question right now.");
		}


        $types = array('config','layout','block_html','collections','reflection','db_ddl','compiled_config','eav','config_integration','config_integration_api','full_page','translate','config_webservice','vertex');
		foreach ($types as $type) {
		    $this->_cacheTypeList->cleanType($type);
		}
		foreach ($this->_cacheFrontendPool as $cacheFrontend) {
		    $cacheFrontend->getBackend()->clean();
		}

		$resultRedirect = $this->resultRedirectFactory->create();
		$resultRedirect->setPath($this->_redirect->getRefererUrl());
		return $resultRedirect;
	}
}
