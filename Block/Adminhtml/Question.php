<?php
namespace AHT\QA\Block\Adminhtml;
class Question extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'AHT_QA';
        $this->_controller = 'adminhtml_question';
        parent::_construct();
    }
}