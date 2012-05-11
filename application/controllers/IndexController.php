<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        
        $appConfig = Zend_Registry::get('Config_App');
        $this->view->storageHost = $appConfig['storage']['host'];
        
        $flashMessages = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger')->getMessages();
        $this->view->messages = $flashMessages;
        
        $this->params = $this->getRequest()->getParams();
        $this->view->identity = $this->_helper->getIdentity();
    }

    /**
     * Display main site, with approved posts
     */
    private function _getPostList($select)
    {
        $adapter = new Zend_Paginator_Adapter_DbTableSelect($select);
        
        $paginator = new Zend_Paginator($adapter);
        $paginator->setCurrentPageNumber($this->_getParam('page'));

        $this->view->paginator = $paginator;
    }

    /**
     * Display main site, with approved posts
     */
    public function indexAction()
    {
        $posts = new Application_Model_DbTable_Posts();
        $select = $posts->select()
            ->where('category = ?', 2)
            ->where('status = ?', "a")
            ->order('moderated DESC')
            ->order('added DESC');

        $this->_getPostList($select);
    }
    
    /**
     * Display content from specified author
     */
    public function authorAction()
    {        
        $author = $this->_getParam('name');

        $posts = new Application_Model_DbTable_Posts();
        $select = $posts->select()
            ->where('author = ?', $author)
            ->where('status = ?', "a")
            ->order('added DESC');

        $this->_getPostList($select);

        $this->view->title = $author;
        $this->view->headTitle($author);
    }

    /**
     * Display posts awaiting moderation
     */
    public function awaitingAction()
    {
        $posts = new Application_Model_DbTable_Posts();
        $select = $posts->select()
            ->where('category IN (0,1)')
            ->where('status = ?', "a")
            ->order('added DESC');

        $this->_getPostList($select);

        $this->view->headTitle('Oczekujące');
        $this->view->title = 'Oczekujące';
    }
    
    /**
     * Display contact form
     */
    public function contactAction()
    {
    }
    
    /**
     * Display rules and regulations
     */
    public function rulesAction()
    {
    }
}