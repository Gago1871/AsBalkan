<?php

class Jk_Resource_Layouthelpers extends Zend_Application_Resource_ResourceAbstract
{
    protected $_options = array(
        'doctype'         => 'XHTML1_STRICT',
        'title'           => 'Site Title',
        'title_separator' => ' :: ',
    );

    public function init()
    {
        // $bootstrap = $this->getBootstrap();
        // $bootstrap->bootstrap('View');
        // $view = $bootstrap->getResource('View');

        // $options = $this->getOptions();

        // $view->doctype($options['doctype']);
        // $view->headTitle()->setSeparator($options['title_separator'])->append($options['title']);

        // die('init layouthelpers');
    }
}