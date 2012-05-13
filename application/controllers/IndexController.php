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
        
        $this->view->identity = $this->_helper->getIdentity();
    }

    /**
     * Display main site, with approved posts
     */
    public function indexAction()
    {
        $postsGateway = new Application_Model_Post_Gateway();
        $posts = $postsGateway->fetchForMain();

        $paginator = new Jk_Paginator(new Zend_Paginator_Adapter_Array($posts->getList()));
        $paginator->setCurrentPageNumber($this->_getParam('page'));

        $this->view->paginator = $paginator;
    }
    
    /**
     * Display content from specified author
     */
    public function authorAction()
    {        
        $author = $this->_getParam('name');

        $postsGateway = new Application_Model_Post_Gateway();
        $posts = $postsGateway->fetchFromAuthor($author);

        $paginator = new Jk_Paginator(new Zend_Paginator_Adapter_Array($posts->getList()));
        $paginator->setCurrentPageNumber($this->_getParam('page'));

        $this->view->paginator = $paginator;

        $this->view->title = $author;
        $this->view->headTitle($author);
    }

    /**
     * Display posts awaiting moderation
     */
    public function awaitingAction()
    {
        $postsGateway = new Application_Model_Post_Gateway();
        $posts = $postsGateway->fetchAwaiting();

        $paginator = new Jk_Paginator(new Zend_Paginator_Adapter_Array($posts->getList()));
        $paginator->setCurrentPageNumber($this->_getParam('page'));

        $this->view->paginator = $paginator;

        $this->view->title = 'Oczekujące';
        $this->view->headTitle('Oczekujące');
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