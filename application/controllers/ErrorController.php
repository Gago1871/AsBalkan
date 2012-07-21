<?php

class ErrorController extends Zend_Controller_Action
{

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        
        if (!$errors || !$errors instanceof ArrayObject) {
            $this->view->message = 'You have reached the error page';
            return;
        }

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->exceptionPageNotFound();
                $priority = Zend_Log::NOTICE;
                break;
            default:
                // EXCEPTION_OTHER
                // application error
                $this->getResponse()->setHttpResponseCode(500); 
                $this->view->exception = $errors->exception;

                switch($errors->exception->getCode()) {
                  
                  case 2002:               
                    $this->exceptionDbConnectionFailed();
                    break;
                    
                  default:
                    $this->view->message = 'Exception caught (' . get_class($errors->exception) . '), but no specific handler in ErrorHandler defined';
                    break;
                  }

                $priority = Zend_Log::CRIT;
                $this->view->message = 'Application error';

                break;
        }

        // Log exception, if logger available
        if ($log = $this->getLog()) {
           $logMsg =
                $this->view->message . ' [httpResponseCode: ' . $this->getResponse()->getHttpResponseCode() . '], ' .
                $errors->exception->getMessage() . ' [exceptionCode: ' . $errors->exception->getCode() . ']';

            // Log error with set priority
            $log->log($logMsg, $priority);

            // Log details to debug.log
            $log->log('Request Parameters: ' . print_r($errors->request->getParams(), true), Zend_Log::DEBUG);
            $log->log('Exception stack: ' . serialize($errors->exception), Zend_Log::DEBUG);
        }
        
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }

        $this->view->request = $errors->request;
    }

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }

    public function exceptionPageNotFound()
    {
        $this->view->message = 'Page not found ' . $_SERVER['REQUEST_URI'];
        $this->_forward('index', 'index', null, array('layerInfo' => 1));
    }

    public function exceptionDbConnectionFailed()
    {
        $this->view->message = 'Nie udało się połączyć z bazą danych...';
        // $this->_forward('index', 'index', null, array('layerInfo' => 1));
        // $this->_helper->layout->setLayout('error');
        $this->renderScript('error/error.phtml');
    }
    
    public function exceptionMemcachedConnectionFailedAction()
    {
        $this->view->message = 'Nie udało się połączyć z Memcached.';
        // $this->_forward('index', 'index', null, array('layerInfo' => 1));
        // $this->_helper->layout->setLayout('error');
        $this->renderScript('error/error.phtml');
    }
}