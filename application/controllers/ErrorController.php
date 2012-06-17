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
                $this->_forward('exception-page-not-found');
                $this->view->message = 'Nie ma takiej stronyXXX';
                $priority = Zend_Log::NOTICE;
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                switch($errors->exception->getCode()) {
                  
                  case 2002:
                    $this->_forward('exception-db-connection-failed');
                    break;
                    
                  default:
                    $this->view->message = 'Exception caught (' . get_class($errors->exception) . '), but no specific handler in ErrorHandler defined';
                    $this->view->exception = $errors->exception;
                    break;
                  }

                $priority = Zend_Log::CRIT;
                $this->view->message = 'Application error';
                break;
        }

        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->log($this->view->message, $priority, $errors->exception);
            $log->log('Request Parameters', $priority, $errors->request->getParams());
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

    public function exceptionPageNotFoundAction()
    {
        $this->view->message = 'Nie ma takiej strony';
        $this->_helper->layout->setLayout('error');
        $this->renderScript('error/404.phtml');
    }

    public function exceptionDbConnectionFailedAction()
    {
        $this->view->message = 'Nie udało się połączyć z bazą danych...';
        $this->_helper->layout->setLayout('error');
        $this->renderScript('error/error.phtml');   
    }
    
    public function exceptionMemcachedConnectionFailedAction()
    {
        $this->view->message = 'Nie udało się połączyć z Memcached.';
        $this->renderScript('error/error.phtml');
    }
}