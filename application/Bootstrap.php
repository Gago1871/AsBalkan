<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initApplication() {
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
    
    /**
     *
     */
    public function _initView() {
        
        $this->bootstrap('layout');
        $layout = $this->getResource('layout');
        $view = $layout->getView();

        $view->doctype('HTML5');
        // $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
        $view->headMeta()->setCharset('utf-8');    
        $view->headMeta()->setName('Description', 'Wyszukane, intrygujące, piękne zdjęcia dla ludzi szukających inspiracji i rozrywki z klasą.');
        $view->headMeta()->setName('Keywords', 'śmieszne zdjęcia, śmieszne fotki, śmieszne obrazki, zdjęcia, fotki, obrazki, oryginalne, inspirujące, poebao');
        $view->headMeta()->setName('robots', 'index,follow');
        $view->headMeta()->setName('author', 'www.webascrazy.net');
        $view->headTitle()->setSeparator(' - ');
        $view->headTitle('poebao');

    }
}