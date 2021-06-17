<?php
namespace AHT\QA\Model\ResourceModel\Question;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	
	protected $_idFieldName = 'qa_id';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('AHT\QA\Model\Question', 'AHT\QA\Model\ResourceModel\Question');
	}
}