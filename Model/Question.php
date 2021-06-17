<?php
namespace AHT\QA\Model;

class Question extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Undocumented function
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource=null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection=null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
    /**
     * @return void
     */

	public function _construct()
	{
		$this->_init('AHT\QA\Model\ResourceModel\Question');
    }
    /**
     * Undocumented function
     *
     * @return string
     */
    public function getName() {
        return $this->getData("name");
    }
    /**
     * Undocumented function
     *
     * @return string
     */
    public function getEmail() {
        return $this->getData("email");
    }
    /**
     * Undocumented function
     *
     * @return string
     */
    public function getQuestion() {
        return $this->getData("question");
    }
    /**
     * Undocumented function
     *
     * @return string
     */
    public function getAnswer() {
        return $this->getData("answer");
    }
    /**
     * Undocumented function
     *
     * @return string
     */
    public function getCreatedAt() {
        return $this->getData("created_at");
    }
    /**
     * Undocumented function
     *
     * @return string
     */
    public function getUpdatedAt() {
        return $this->getData("updated_at");
    }
    /**
     * Undocumented function
     *
     * @return string
     */
    public function getImagePath() {
        return $this->getData("image_path");
    }
    /**
     * Undocumented function
     *
     * @return int
     */
    public function getQaId() {
        return $this->getData("qa_id");

    }
    /**
     * Undocumented function
     *
     * @return int
     */
    public function getStoreId() {
        return $this->getData("store_id");

    }
    /**
     * Undocumented function
     *
     * @return int
     */
    public function getProductId() {
        return $this->getData("product_id");
    }
    /**
     * Undocumented function
     *
     * @return int
     */
    public function getUserId() {
        return $this->getData("user_id");
    }
    /**
     * Undocumented function
     *
     * @return int
     */
    public function getCheck() {
        return $this->getData("check");
    }
    /**
     * Undocumented function
     *
     * @param string $name
     * @return null
     */
    public function setName($name) {
        return $this->setData("name", $name);
    }
    /**
     * Undocumented function
     *
     * @param int
     * @return null
     */
    public function setProductId($productId) {
        return $this->setData("product_id", $productId);
    }
    /**
     * Undocumented function
     *
     * @param string $email
     * @return null
     */
    public function setEmail($email) {
        return $this->setData("email", $email);
    }
    /**
     * Undocumented function
     *
     * @param string $question
     * @return null
     */
    public function setQuestion($question) {
        return $this->setData("question", $question);

    }
    /**
     * Undocumented function
     *
     * @param string $answer
     * @return null
     */
    public function setAnswer($answer) {
        return $this->setData("answer", $answer);
    }
    /**
     * Undocumented function
     *
     * @param string $answer
     * @return null
     */
    public function setCheck($check) {
        return $this->setData("check", $check);
    }
}
