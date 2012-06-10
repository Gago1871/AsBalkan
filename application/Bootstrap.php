<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * 
     */
    protected function _initApplication()
    {
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

        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial(
            'my_pagination_control.phtml'
        );
    }

    /**
     * Enable loading Jk_Model_* classes from jk/models/* dir - mind plural in folder and singular in model name
     */
    public function _initAutoloaders()
    {
        $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
            'basePath' => APPLICATION_PATH . '/../library/Jk',
            'namespace' => 'Jk_'
        ));

        $resourceLoader->addResourceTypes(array(
            'jkmodels' => array(
                'namespace' => 'Model_',
                'path' => 'Models')
            )
        );
    }

    protected function _initPlugins()
    {
        // Init custom plugin
        // $front = $this->getPluginResource('FrontController')->getFrontController();
        // $front = Zend_Controller_Front::getInstance();
        // $front->registerPlugin(new Poebao_Controller_Plugin_UploadForm());
    }

    protected function _initActionHelpers()
    {
        Zend_Controller_Action_HelperBroker::addPrefix('Poebao_Action_Helper');
    }

    protected function _initConstants()
    {
        $registry = Zend_Registry::getInstance();
        $registry->constants = new Zend_Config( $this->getApplication()->getOption('constants') );
    }

    /**
     * 
     */
    public function _initTranslator()
    {  
        $translator = new Zend_Translate('array', APPLICATION_PATH . '/lang/pl.php', 'pl');
        $translator->setLocale('pl');

        Zend_Registry::set('Zend_Translate', $translator);
        return $translator;
    }
    
    /**
     *
     */
    public function _initHead() {

        $this->bootstrap('View');
        $view = $this->getResource('View');

        $view->headMeta()->setName('Description', 'Wyszukane, intrygujące, piękne zdjęcia dla ludzi szukających inspiracji i rozrywki z klasą.');
        $view->headMeta()->setName('Keywords', 'śmieszne zdjęcia, śmieszne fotki, śmieszne obrazki, zdjęcia, fotki, obrazki, oryginalne, inspirujące, poebao');
        $view->headMeta()->setName('robots', 'index,follow');
        $view->headMeta()->setName('author', 'www.webascrazy.net');
        $view->headTitle()->setSeparator(' - ');
        $view->headTitle('poebao');
    }
}