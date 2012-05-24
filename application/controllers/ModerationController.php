<?php

class ModerationController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */

        $appConfig = Zend_Registry::get('Config_App');
        $this->view->storageHost = $appConfig['storage']['host'];

        $this->view->identity = $this->_helper->getIdentity();

        $this->requestParams = $this->getRequest()->getParams();
    }

    /**
     * Moderation list action
     *
     * @since 2012-04-24
     * @author Jakub Kułak <jakub.kulak@gmail.com>
     */
    public function listAction()
    {
        $this->view->headTitle('Moderacja');
        $this->view->title = 'Moderacja';

        $posts = array();

        $form = new Application_Form_Moderation();
        $formData = $this->requestParams;
        $postGateway = new Application_Model_Post_Gateway();

        if (isset($this->requestParams['submit'])) {
            if ($form->isValid($formData)) {
                $posts = $postGateway->fetchForModeration($formData);
            }
        } else {
            $posts = $postGateway->fetchForModeration(array('category' => 0, 'removed' => 0));
        }

        $paginator = new Jk_Paginator(new Zend_Paginator_Adapter_Array($posts->getList()));
        $paginator->setCurrentPageNumber($this->_getParam('page'));

        $this->view->paginator = $paginator;

        $this->view->posts = $posts;
        $this->view->form = $form;
    }

    /**
     * 
     */
    public function setcategoryAction()
    {
        $id = $this->_getParam('id');
        $category = $this->_getParam('category');

        $postGateway = new Application_Model_Post_Gateway();

        $post = new Application_Model_Post(array(
            'id' => $id,
            'category' => $category,
            'moderated' => date('Y-m-d H:i:s'),
            ), $postGateway);
        $post->save();

        $this->_helper->redirector->gotoRouteAndExit(array(), 'moderation');
    }

    /**
     * 
     */
    public function flagAction()
    {
        $id = $this->_getParam('id');
        $flag = $this->_getParam('flag');
        $value = $this->_getParam('value');

        $postGateway = new Application_Model_Post_Gateway();

        $post = new Application_Model_Post(array(
            'id' => $id,
            'flag_' . $flag => $value,
            ), $postGateway);
        $post->save();

        $this->_helper->redirector->gotoRouteAndExit(array(), 'moderation');
    }

    /**
     * 
     */
    public function statusAction()
    {
        $id = $this->_getParam('id');
        $status = $this->_getParam('status');

        $postGateway = new Application_Model_Post_Gateway();

        $post = new Application_Model_Post(array(
            'id' => $id,
            'status' => $status,
            ), $postGateway);
        $post->save();

        $this->_helper->redirector->gotoRouteAndExit(array(), 'moderation');
    }

}
        