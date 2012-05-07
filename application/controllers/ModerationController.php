<?php

class ModerationController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */

        $appConfig = Zend_Registry::get('Config_App');
        $this->view->storageHost = $appConfig['storage']['host'];

        $this->view->identity = $this->_helper->getIdentity();

        $this->posts = new Application_Model_DbTable_Posts();
        $this->requestParams = $this->getRequest()->getParams();
    }


    /**
     *
     */
    private function _getPostList($select)
    {
        $adapter = new Zend_Paginator_Adapter_DbTableSelect($select);
        
        $paginator = new Zend_Paginator($adapter);
        $paginator->setCurrentPageNumber($this->_getParam('page'));

        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial(
            'my_pagination_control.phtml'
        );

        $this->view->paginator = $paginator;
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

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                // $postsModel = new Application_Model_DbTable_Posts();
                // $posts = $postsModel->fetchFromCategory($formData['category'], $formData['nsfw'], $formData['removed']);
                
                $posts = new Application_Model_DbTable_Posts();
                $select = $posts->select()
                    ->where('category = ?', $formData['category'])
                    ->order('added DESC');
                
                if (!$formData['removed']) {
                    $select->where('status = ?', "a");
                }

                if (!$formData['nsfw']) {
                    $select->where('flag_nsfw = ?', 0);
                }

                $this->_getPostList($select);
            }
        } else {
            $posts = new Application_Model_DbTable_Posts();
            $select = $posts->select()
                ->where('category = ?', 0)
                // ->where('status = ?', "a")
                ->order('added DESC');

            $this->_getPostList($select);
        }

        $this->view->posts = $posts;
        $this->view->form = $form;
    }

    /**
     * 
     */
    public function setcategoryAction()
    {
        $id = $this->requestParams['id'];
        $category = $this->requestParams['category'];

        $this->posts->setCategory($id, $category);
        $this->_helper->redirector->gotoRouteAndExit(array(), 'moderation');
    }

    /**
     * 
     */
    public function flagAction()
    {
        $id = $this->requestParams['id'];
        $flag = $this->requestParams['flag'];
        $value = $this->requestParams['value'];

        $this->posts->setFlag($id, $flag, $value);
        $this->_helper->redirector->gotoRouteAndExit(array(), 'moderation');
    }

    /**
     * 
     */
    public function statusAction()
    {
        $id = $this->requestParams['id'];
        $status = $this->requestParams['status'];

        $this->posts->setStatus($id, $status);
        $this->_helper->redirector->gotoRouteAndExit(array(), 'moderation');
    }

}
        