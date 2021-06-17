<?php
namespace AHT\QA\Block\Adminhtml\Question\Edit;

use Magento\Search\Controller\RegistryConstants;

/**
 * Class GenericButton
 */
class GenericButton
{
    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * Registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    protected $questionFactory;
    protected $questionResource;
    protected $context;
    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \AHT\QA\Model\QuestionFactory $questionFactory,
        \AHT\QA\Model\ResourceModel\Question $questionResource
    ) {
        $this->questionFactory = $questionFactory;
        $this->questionResource = $questionResource;
        $this->urlBuilder = $context->getUrlBuilder();
        $this->registry = $registry;
        $this->context = $context;
    }

    /**
     * Return the synonyms group Id.
     *
     * @return int|null
     */
    public function getId()
    {
        $contact = $this->registry->registry('contact');
        return $contact ? $contact->getId() : null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
    /**
     * Return Question ID
     *
     * @return int|null
     */
    public function getQaId()
    {
        try {
            $question = $this->questionFactory->create();
            $this->questionResource->load($question, $this->context->getRequest()->getParam('qa_id'));
            if (!$question->getId()) {
                throw new NoSuchEntityException(__('The CMS block with the "%1" ID doesn\'t exist.', $this->context->getRequest()->getParam('qa_id')));
            }
            return $question->getId();
        } catch (NoSuchEntityException $e) {
        }
        return null;
    }
}
