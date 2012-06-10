<?php

class Poebao_Action_Helper_UploadForm extends Zend_Controller_Action_Helper_Abstract
{
    public function direct($options = null)
    {
        $request  = $this->getRequest();
        // $view     = $this->getActionController()->view;

        // Create new form
        $this->params = $request->getParams();
        $fromFile = (isset($this->params['uploadfromfile']) && (1 == $this->params['uploadfromfile']));

        $url = Zend_Controller_Action_HelperBroker::getStaticHelper('url');

        $form = new Application_Form_Post(array('action' => $url->url(array(), 'postupload'), 'uploadfromfile' => $fromFile));

        // set form options here...

        // $view->form = $form; // optional - assign form directly to the view

        return $form;
    }
}