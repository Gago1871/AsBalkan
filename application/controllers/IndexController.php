<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        
        $appConfig = Zend_Registry::get('Config_App');
        $this->view->storageHost = $appConfig['storage']['host'];
    }

    /**
     * Display main site, with approved posts
     */
    public function indexAction() {

        $posts = new Application_Model_DbTable_Posts();
        $this->view->posts = $posts->fetchAll('`status_promoted`="1" AND`status`="a"', 'added DESC');
    }
    
    /**
     * Display content from specified author
     */
    public function authorAction()
    {
        $params = $this->getRequest()->getParams();
        $author = $params['name'];

        $posts = new Application_Model_DbTable_Posts();
        $this->view->posts = $posts->fetchAll('`author`="' . $author . '" AND `status`="a"', 'added DESC');
        $this->view->author = $author;
        
        $this->view->headTitle($author);
    }

    /**
     * Display posts awaiting moderation
     */
    public function awaitingAction()
    {
        $posts = new Application_Model_DbTable_Posts();
        $this->view->posts = $posts->fetchAll('`status_waiting`="1" AND `status_promoted`="0" AND `status`="a"', 'added DESC');

        $this->view->headTitle('Oczekujące');
    }
    

    /**
     * Display post
     */
    public function viewAction()
    {
        $params = $this->getRequest()->getParams();
        $id = $params['id'];

        $posts = new Application_Model_DbTable_Posts();
        $post = $posts->get($id);
        $this->view->post = $post;

        $this->view->headTitle($post['title']);
        
        $message = array('type' => 'success', 'content' => 'Twój post został dodany.');
        $this->view->message = $message;
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

