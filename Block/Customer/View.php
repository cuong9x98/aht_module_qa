<?php
namespace AHT\QA\Block\Customer;

use Magento\Catalog\Model\Product;
use AHT\QA\Model\Question;

/**
 * Customer Review detailed view block
 *
 * @api
 * @author      Magento Core Team <core@magentocommerce.com>
 * @since 100.0.2
 */
class View extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * Customer view template name
     *
     * @var string
     */
    protected $_template = 'AHT_QA::customer/view.phtml';

    /**
     * Catalog product model
     *
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Review model
     *
     * @var \AHT\QA\Model\QuestionFactory
     */
    protected $_questionFactory;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \AHT\QA\Model\QuestionFactory $questionFactory
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \AHT\QA\Model\QuestionFactory $questionFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        array $data = []
    ) {
        $this->productRepository = $productRepository;
        $this->_questionFactory = $questionFactory;
        $this->currentCustomer = $currentCustomer;
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Initialize review id
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setQaId($this->getRequest()->getParam('qa_id', false));
    }

    /**
     * Get product data
     *
     * @return Product
     */
    public function getProductData()
    {
        if ($this->getQaId()) {
            $product = $this->productRepository->getById($this->getQuestionData()->getProductId());
            return $product;
        }
        return null;
    }

    /**
     * Get question data
     *
     * @return Question
     */
    public function getQuestionData()
    {
        if ($this->getQaId()) {
            $question = $this->_questionFactory->create()->load($this->getQaId());
            return $question;
        }
        return null;
        
    }
    /**
     * Get formatted date
     *
     * @param string $date
     * @return string
     */
    public function dateFormat($date)
    {
        return $this->formatDate($date, \IntlDateFormatter::LONG);
    }

}
