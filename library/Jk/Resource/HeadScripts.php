<?php

/**
 * Zend Resource Plugin that loads scripts to html head section
 */
class Jk_Resource_Headscripts extends Zend_Application_Resource_ResourceAbstract
{
    protected $_options = array(
        'js' => array(),
    );

    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('View');
        $view = $bootstrap->getResource('View');

        $options = $this->getOptions();

        foreach ($options['js'] as $key => $value) {

            $view->headScript()->appendFile($view->baseUrl() . $value);
        }
    }
}