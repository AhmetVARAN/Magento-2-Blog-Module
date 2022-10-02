<?php

namespace Ahmet\Blog\Block;

use Ahmet\Blog\Model\BlogFactory;
use Magento\Framework\View\Element\Template;

class Index extends \Magento\Framework\View\Element\Template
{
    protected BlogFactory $blogFactory;

    public function __construct(
        BlogFactory $blogFactory,
        Template\Context $context, array $data = [])
    {
        $this->blogFactory = $blogFactory;
        parent::__construct($context, $data);
    }

    //Function for getting blog data
    public function getBlogEntities(){
        $blog = $this->blogFactory->create();
        $colletcion = $blog->getCollection();
        if ($colletcion){
            return $colletcion;
        }
        return [];
    }
}
