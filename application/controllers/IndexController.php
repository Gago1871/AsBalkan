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

        $this->params = $this->getRequest()->getParams();

        // this param is set in errorController
        if (isset($this->params['layerInfo']) AND $this->params['layerInfo'] == 1) {
            $this->view->layerInfo = true;
        }   

        // set up initial message to be displayed
        $message = array();

        if (!empty($message)) {
            $this->view->messages[] = $message;
        }
    }

    /**
     * Display main site, with approved posts
     */
    public function indexAction()
    {
        $postsGateway = new Application_Model_Post_Gateway();
        $select = $postsGateway->fetchForMain();

        $adapter = new Poebao_Paginator_Adapter_DbSelect($select);

        $paginator = new Zend_Paginator($adapter);
        $paginator->setCurrentPageNumber($this->_getParam('page'));

        $this->view->paginator = $paginator;

        $this->view->postViewRoute = 'postview';
        $this->view->blockPostViewRoute = 'home';

        $this->view->canonicalUrl = $this->view->serverUrl();
        $this->view->headLink()->headLink(array('rel' => 'canonical', 'href' => $this->view->canonicalUrl), 'PREPEND');

        // Open Graph Protocol (see more: http://mgp.me)
        $og = new Jk_Og('poebao');
        $og->fbAppId = Zend_Registry::getInstance()->constants->fb->appId;
        $og->title = 'Poebao.pl';
        $images = array();
        foreach ($paginator as $key => $value) {
            $images[] = $value->image('min');
        }
        $og->image = $images;
        $og->type = 'article';
        $og->url = $this->view->canonicalUrl;
        $this->view->og = $og->getMetaData();
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

        $this->view->postViewRoute = 'author-postview';
        $this->view->blockPostViewRoute = 'home';

        // Open Graph Protocol (see more: http://mgp.me)
        $og = new Jk_Og('poebao');
        $og->fbAppId = Zend_Registry::getInstance()->constants->fb->appId;
        $og->title = $this->view->title = $author;
        $images = array();
        foreach ($paginator as $key => $value) {
            $images[] = $value->image('min');
        }
        $og->image = $images;
        $og->type = 'article';
        $og->url = $this->view->canonicalUrl;
        $this->view->og = $og->getMetaData();
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

        $this->view->postViewRoute = 'awaiting-postview';
        $this->view->blockPostViewRoute = 'awaiting';

        // Open Graph Protocol (see more: http://mgp.me)
        $og = new Jk_Og('poebao');
        $og->fbAppId = Zend_Registry::getInstance()->constants->fb->appId;
        $og->title = 'Poebao.pl - oczekujące';
        $images = array();
        foreach ($paginator as $key => $value) {
            $images[] = $value->image('min');
        }
        $og->image = $images;
        $og->type = 'article';
        $og->url = $this->view->canonicalUrl;
        $this->view->og = $og->getMetaData();
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
        $this->view->blockPostViewRoute = 'home';
    }
}