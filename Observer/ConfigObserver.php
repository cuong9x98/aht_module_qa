<?php

namespace AHT\QA\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Psr\Log\LoggerInterface as Logger;
use Magento\Backend\App\Action\Context;

class ConfigObserver implements ObserverInterface
{
    protected $_cacheTypeList;

    protected $_cacheFrontendPool;
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param Logger $logger
     */
    public function __construct(
        Logger $logger,
        Context $context,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
    )
    {
        $this->logger = $logger;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
    }

    public function execute(EventObserver $observer)
    {
        $types = array('full_page');
        foreach ($types as $type) {
            $this->_cacheTypeList->cleanType($type);
        }
    }
}
