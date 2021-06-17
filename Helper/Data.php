<?php
namespace AHT\QA\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    protected $_customerSession;
    public $scopeConfig;

    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_customerSession = $customerSession;
        $this->scopeConfig = $scopeConfig;
    }

    public function getCustomerSession() {
        return $this->_customerSession;
    }

    public function getValueTemplate()
    {
        return $this->scopeConfig->getValue('question_pending/question/is_logins', ScopeInterface::SCOPE_STORE);
    }
}
