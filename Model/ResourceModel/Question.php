<?php
namespace AHT\QA\Model\ResourceModel;

use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Model\AbstractModel;

class Question extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context
	) {

		parent::__construct($context);
	}
	
	protected function _construct() {
		$this->_init('aht_question', 'qa_id');
	}

	
}
