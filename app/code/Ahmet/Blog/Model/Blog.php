<?php

namespace Ahmet\Blog\Model;

class Blog extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'ahmet_blog_blog_entity';

    protected $_cacheTag = 'ahmet_blog_blog_entity';

    protected $_eventPrefix = 'ahmet_blog_blog_entity';

    protected function _construct()
    {
        $this->_init('Ahmet\Blog\Model\ResourceModel\Blog');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}
