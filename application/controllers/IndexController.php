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

        $message = array(
            'title' => 'To jest tytuł komunikatu - ogłaszamy, że każdy kto wrzuci obrazek, może liczyć, że wyląduje on w poczekalni!',
            'content' => 'Tu jest druga linia komunikatu i tu <a class="fancy-hover" href="">może być link >></a>',
            'type' => 'message-type-info'
            );

        $this->view->messages[] = $message;
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

        $this->view->postViewRoute = 'postview';

        $this->view->postForm = $this->_helper->UploadForm();
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

        $this->view->postForm = $this->_helper->UploadForm();
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

        $this->view->postForm = $this->_helper->UploadForm();
    }
    
    /**
     * Display contact form
     */
    public function contactAction()
    {
        $this->view->postForm = $this->_helper->UploadForm();
    }
    
    /**
     * Display rules and regulations
     */
    public function rulesAction()
    {
        $this->view->postForm = $this->_helper->UploadForm();
    }
}