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
class Poebao_Controller_Plugin_UploadForm extends Zend_Controller_Plugin_Abstract
{
    /**
     * postDispatch() plugin hook -- check for actions in stack, and dispatch if any found
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
    }
    
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
    }
    
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $this->createForm($request);
    }
    
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
    }
    
    private function createForm($request)
    {
        $view = Zend_Layout::getMvcInstance()->getView();

        // Create new form
        $this->params = $request->getParams();
        $fromFile = (isset($this->params['uploadfromfile']) && (1 == $this->params['uploadfromfile']));

        $url = Zend_Controller_Action_HelperBroker::getStaticHelper('url');

        $form = new Application_Form_Post(array('action' => $url->url(array(), 'postupload'), 'uploadfromfile' => $fromFile));
        $view->postForm = $form;
    }
}