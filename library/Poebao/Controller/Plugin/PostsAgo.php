<?php

/** Zend_Controller_Plugin_Abstract */
// require_once 'Zend/Controller/Plugin/Abstract.php';

/**
 * 
 *
 * @uses       Zend_Controller_Plugin_Abstract
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Plugins
 */
class Poebao_Controller_Plugin_PostsAgo extends Zend_Controller_Plugin_Abstract
{
    public function init()
    {
        $this->view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');
    }

    /**
     * postDispatch() plugin hook -- check for actions in stack, and dispatch if any found
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        $this->init();
        $category = 2;

        if (isset($this->view->category)) {
            $category = $this->view->category;
        }

        if (isset($this->view->post)) {
            $category = $this->view->post->category;
        }

        $offset = Zend_Registry::getInstance()->constants->app->blocks->postsAgo->offset;

        $block = new stdClass();
        $block->postsAgo->offset = $offset;

        $postsGateway = new Application_Model_Post_Gateway();
        $posts = $postsGateway->fetchPostsAgo($category);
        $block->postsAgo->posts = $posts->getList();

        $this->view->block = $block;
    }
}