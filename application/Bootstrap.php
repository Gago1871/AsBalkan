<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initApplication() {
        // Load configuration from file, put it in the registry
        $appConfig = $this->getOption('app');
        Zend_Registry::set('Config_App', $appConfig);

        $config = $this->getOptions();
        Zend_Registry::set('Zend_Config', $config);

        // Start routing
        $frontController = Zend_Controller_Front::getInstance();
        $router = $frontController->getRouter();

        // In case I want to turn on translation
        // Zend_Controller_Router_Route::setDefaultTranslator($translator);
        $routes = new Zend_Config_Xml(APPLICATION_PATH . '/configs/routes.xml', APPLICATION_ENV);
        $router->removeDefaultRoutes();
        $router->addConfig($routes, 'routes');

        Zend_Controller_Action_HelperBroker::addPrefix('Jk_Helper');
    }

    public function _initTranslator()
    {  
      $translator = new Zend_Translate('array', APPLICATION_PATH . '/lang/pl.php', 'pl');
      $translator->setLocale('pl');

      Zend_Registry::set('Zend_Translate', $translator);
    }

    public function _initPaginator()
    {
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial(
            'my_pagination_control.phtml'
        );
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

        $config = Zend_Registry::get('Zend_Config');

        $view->headScript()->appendFile($view->baseUrl() . $config['js']['jquery']['filename']);
        $view->headScript()->appendFile($view->baseUrl() . $config['js']['app']['filename']);
        $view->headScript()->appendFile($view->baseUrl() . '/js/jquery.timeago.js');
        $view->headScript()->appendFile($view->baseUrl() . '/js/jquery.timeago.pl.js');
    }
}