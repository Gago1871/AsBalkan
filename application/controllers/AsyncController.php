<?php

class AsyncController extends Zend_Controller_Action
{

    public function init()
    {
        // Check if request is XHR, otherwise throw 404
        if (!$this->_request->isXmlHttpRequest()) {
             throw new Zend_Controller_Action_Exception('Seem like this page doesn\'t exist ;)', 404);
        }

        $this->params = $this->getRequest()->getParams();
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