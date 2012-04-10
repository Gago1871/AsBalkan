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
    public function indexAction()
    {
        $this->view->title = "Poebao";
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
        $this->view->title = 'Autor: ' . $author;
        $posts = new Application_Model_DbTable_Posts();
        $this->view->posts = $posts->fetchAll('`author`="' . $author . '" AND `status`="a"', 'added DESC');
    }

    /**
     * Display posts awaiting moderation
     */
    public function awaitingAction()
    {
        $this->view->title = 'Oczekujące';
        $posts = new Application_Model_DbTable_Posts();
        $this->view->posts = $posts->fetchAll('`status_waiting`="1" AND `status_promoted`="0" AND `status`="a"', 'added DESC');
    }
    

    /**
     * Display post
     */
    public function viewAction()
    {
        $params = $this->getRequest()->getParams();
        $id = $params['id'];
        $this->view->title = 'Post ' . $id;
        $posts = new Application_Model_DbTable_Posts();
        $this->view->post = $posts->get($id);
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

