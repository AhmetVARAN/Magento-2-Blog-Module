<?php

namespace Ahmet\Blog\Controller\adminhtml\post;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Ahmet\Blog\Model\BlogFactory
     */
    var $blogFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Ahmet\Blog\Model\BlogFactory $blogFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Ahmet\Blog\Model\BlogFactory $blogFactory
    ) {
        parent::__construct($context);
        $this->blogFactory = $blogFactory;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $this->_redirect('ahmet/blog/addrow');
            return;
        }
        try {
            $rowData = $this->blogFactory->create();
            $rowData->setData($data);
            if (isset($data['id'])) {
                $rowData->setPostId($data['id']);
            }
            $rowData->save();
            $this->messageManager->addSuccess(__('Post has been successfully saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('ahmet/blog/index');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ahmet_Blog::save');
    }
}
