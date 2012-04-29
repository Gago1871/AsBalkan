<?php

/**
 * Action Helper for user functionality
 *
 * @uses Zend_Controller_Action_Helper_Abstract
 */
class Jk_Helper_GetIdentity extends Zend_Controller_Action_Helper_Abstract
{
	
	public function login($name, $options = null)
    {
        $module  = $this->getRequest()->getModuleName();
        $front   = $this->getFrontController();
        $default = $front->getDispatcher()
                         ->getDefaultModule();
        if (empty($module)) {
            $module = $default;
        }

        echo 'jestem jk action helper';
        
    }

    public function getIdentity($value='')
    {
        $auth = Zend_Auth::getInstance();

        if ($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            return $identity;
        }

        return false;
    }
}