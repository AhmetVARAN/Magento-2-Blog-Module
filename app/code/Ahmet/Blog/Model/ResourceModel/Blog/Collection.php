<?php

namespace Ahmet\Blog\Model\ResourceModel\Blog;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'post_id';
    protected $_eventPrefix = 'ahmet_blog_blog_entity';
    protected $_eventObject = 'blog_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ahmet\Blog\Model\Blog', 'Ahmet\Blog\Model\ResourceModel\Blog');
    }

}
