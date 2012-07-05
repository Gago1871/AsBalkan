<?php

/**
 * Zend Resource Plugin that loads scripts to html head section
 */
class Jk_Resource_HeadLinks extends Zend_Application_Resource_ResourceAbstract
{
    protected $_options = array(
        'css' => array(),
    );

    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('View');
        $view = $bootstrap->getResource('View');

        $options = $this->getOptions();

        foreach ($options['css'] as $key => $value) {
            $view->headLink()->appendStylesheet($view->baseUrl() . $value);
        }
    }
}