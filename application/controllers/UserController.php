<?php

class UserController extends Zend_Controller_Action
{

    public function init()
    {
    }

    /**
     * Login user action
     *
     * @since 2012-04-23
     * @author Jakub Kułak <jakub.kulak@gmail.com>
     */
    public function loginAction()
    {
        $form = new Application_Form_Login();
        
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {

                $filename = Zend_Registry::getInstance()->constants->app->admin->passwordFile;
                $realm = 'Admin';

                $auth = Zend_Auth::getInstance();
                $adapter = new Zend_Auth_Adapter_Digest($filename, $realm, $formData['login'], $formData['password']);                                
                $result = $auth->authenticate($adapter);

                if ($result->isValid()) {
                    $identity = $result->getIdentity();
                    $this->_redirect($this->_helper->url->url(array(), 'moderation'));
                } else {
                    $form->setErrors(array('Invalid user/pass'));
                    $form->addDecorator('Errors', array('placement' => 'prepend'));
                }
            }
        }

        $this->view->form = $form;    
    }

    /**
     * Logout user action
     *
     * @since 2012-04-23
     * @author Jakub Kułak <jakub.kulak@gmail.com>
     */
    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect($this->_helper->url->url(array(), 'home'));
    }
}