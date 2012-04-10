<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initApplication()
    {
        // Load configuration from file, put it in the registry
        $appConfig = $this->getOption('app');
        Zend_Registry::set('Config_App', $appConfig);

        // Start routing
        $frontController = Zend_Controller_Front::getInstance();
        $router = $frontController->getRouter();

        // In case I want to turn on translation
        // Zend_Controller_Router_Route::setDefaultTranslator($translator);
        $routes = new Zend_Config_Xml(APPLICATION_PATH . '/configs/routes.xml', APPLICATION_ENV);
        //$router->removeDefaultRoutes();
        $router->addConfig($routes, 'routes');
    }
}