<?php

class AsyncController extends Zend_Controller_Action
{

    public function init()
    {
        //  Initialize action controller here 
        
        // $appConfig = Zend_Registry::get('Config_App');
        // $this->view->storageHost = $appConfig['storage']['host'];
        
        // $flashMessages = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger')->getMessages();
        // $this->view->messages = $flashMessages;

        $this->params = $this->getRequest()->getParams();
        // $this->view->identity = $this->_helper->getIdentity();
    }

    /**
     * 
     */
    public function validateFormAction()
    {
        $fromFile = (isset($this->params['uploadfromfile']) && (1 == $this->params['uploadfromfile']));
        $form = new Application_Form_Post(array('action' => $this->_helper->url->url(array(), 'postupload'), 'uploadfromfile' => $fromFile));
        $form->isValid($this->params);

        $result = $form->getMessages();

        $this->_helper->json($result);
    }
}