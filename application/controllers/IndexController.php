<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        
        $appConfig = Zend_Registry::get('Config_App');
        $this->view->storageHost = $appConfig['storage']['host'];
        
        $flashMessages = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger')->getMessages();
        $this->view->messages = $flashMessages;
        
        $this->params = $this->getRequest()->getParams();

        $this->postForm = new Application_Form_Post();
        $fromFile = false;

        if (isset($this->params['file'])) {
            $this->view->showUpload = true;
            if ($this->params['file'] == 1) {
                $fromFile = true;
            }
        } 

        $this->postForm->setmyvar($fromFile);
        $this->postForm->startform(); 

        $this->view->postForm = $this->postForm;
        
        $objRoute = Zend_Controller_Front::getInstance()->getRouter();

        $objRoute =  $objRoute->getRoute('post');

        // var_dump( $objRoute);

        $auth = Zend_Auth::getInstance();

        if ($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            $this->view->identity = $identity;
        }
    }

    /**
     * Display main site, with approved posts
     */
    public function indexAction() {

        $posts = new Application_Model_DbTable_Posts();
        $this->view->posts = $posts->fetchAll('`category`="2" AND `status`="a"', 'added DESC');
    }
    
    /**
     * Display content from specified author
     */
    public function authorAction()
    {
        $author = $this->params['name'];

        $posts = new Application_Model_DbTable_Posts();
        $this->view->posts = $posts->fetchAll('`author`="' . $author . '" AND `status`="a"', 'added DESC');
        // $this->view->author = $author;
        
        $this->view->title = $author;
        $this->view->headTitle($author);
    }

    /**
     * Display posts awaiting moderation
     */
    public function awaitingAction()
    {
        $posts = new Application_Model_DbTable_Posts();
        $this->view->posts = $posts->fetchAll('`category`="1" AND `status`="a"', 'added DESC');

        $this->view->headTitle('Oczekujące');
        $this->view->title = 'Oczekujące';
    }
    

    /**
     * Display post
     */
    public function viewAction()
    {
        $id = $this->params['id'];

        $posts = new Application_Model_DbTable_Posts();
        $post = $posts->get($id);
        $this->view->post = $post;

        $this->view->headTitle($post['title']);
        
        $message = array('type' => 'success', 'content' => 'Twój post został dodany.');
        $this->view->message = $message;
    }
    
    public function addAction()
    {
        $postForm = $this->postForm;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($postForm->isValid($formData)) {

                $www = $postForm->getValue('www');
                
                $appConfig = Zend_Registry::get('Config_App');
                $storage = $appConfig['storage']['location']; // /var/www/poebao-cdn
                $xerocopy = $appConfig['xerocopy'];
                $id = Jk_Url::createUniqueId(); // abcd123
                $hashedDir = Jk_File::getHashedDirStructure($id); // a/b/c/d123
                
                $file = $postForm->file->getFileName();
                $postForm->file->receive();
                
                if ($postForm->file->isReceived()) {
                    
                    $fileInfo = pathinfo($file);
                    $normalizedFilename = Jk_Url::normalize($fileInfo['filename']);
                    
                    $thumbLoc = array();
                    $thumbFilename = array();

                    // start xerocopy magic
                    foreach ($xerocopy['format'] as $key => $format) {
                        if (isset($format['width'])) {
                            $image = Jk_Image::resizeImage($file, $format['width']);
                        } else {
                            $image = Jk_Image::createImageFromFile($file);
                        }

                        $thumbLoc[$key] = $storage . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . $hashedDir;
                        Jk_File::createDir($thumbLoc[$key]);

                        if (!isset($format['type'])) {
                            $format['type'] = $fileInfo['extension'];
                        }

                        if (isset($format['filename'])) {
                            $tmpName = $format['filename'];
                        } else {
                            $tmpName = $normalizedFilename;
                        }
                        
                        $thumbFilename[$key] = $tmpName . '.' . $format['type'];
                        $thumbFile[$key] = $thumbLoc[$key] . '-' . $tmpName . '.' . $format['type'];
                        Jk_Image::saveImage($image, $thumbFile[$key]);
                    }
                }

                $title = $postForm->getValue('title');
                $author = $postForm->getValue('author');
                $agreement = $postForm->getValue('agreement');
                
                // read source of the file
                $source = 'HD';
                
                $posts = new Application_Model_DbTable_Posts();
                $posts->add($id, $thumbFilename['thumb'], $title, $author, $fileInfo['filename'], $agreement, $source);
                
                // Zend_Controller_Action_Helper_Redirector::goto
                $message = array('type' => 'success', 'content' => 'Twój post został dodany.');
                $this->_helper->getHelper('FlashMessenger')->addMessage($message);

                // $this->_helper->redirector->gotoSimple('view', 'index', null, array('id' => $id, 'title' => $title));
                $this->_helper->redirector->gotoRouteAndExit(array('id' => $id, 'title' => $title, 'dupa' => 'dupaa'), 'view');
            } else {

                
                $message = array('type' => 'failure', 'content' => 'You`re doing it wrong...');
                $this->_helper->getHelper('FlashMessenger')->addMessage($message);
                                $postForm->populate($formData);
            }
        }

        $this->view->headTitle('Dodaj post');
    }

    /**
     * Login user action
     *
     * @since 2012-04-23
     * @author Jakub Kułak <jakub.kulak@gmail.com>
     */
    public function loginAction()
    {
        $form = new Application_Form_Login();
        
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {

                $filename = APPLICATION_PATH . '/users/.htdigest';
                $realm = 'Admin';

                $auth = Zend_Auth::getInstance();
                $adapter = new Zend_Auth_Adapter_Digest($filename, $realm, $formData['login'], $formData['password']);                                
                $result = $auth->authenticate($adapter);

                if ($result->isValid()) {
                    $identity = $result->getIdentity();
                    $this->_redirect($this->_helper->url->url(array(), 'moderation'));
                } else {
                    $form->setErrors(array('Invalid user/pass'));
                    $form->addDecorator('Errors', array('placement' => 'prepend'));
                }
            }
        }

        $this->view->form = $form;    
    }

    /**
     * Moderation list action
     *
     * @since 2012-04-24
     * @author Jakub Kułak <jakub.kulak@gmail.com>
     */
    public function moderationAction()
    {
        $this->view->headTitle('Moderacja');
        $this->view->title = 'Moderacja';

        $posts = array();

        $form = new Application_Form_Moderation();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {

                $postsModel = new Application_Model_DbTable_Posts();
                $posts = $postsModel->fetchFromCategory($formData['category'], $formData['nsfw'], $formData['removed']);
            }
        }

        $this->view->posts = $posts;
        $this->view->form = $form;
    }

    /**
     * Logout user action
     *
     * @since 2012-04-23
     * @author Jakub Kułak <jakub.kulak@gmail.com>
     */
    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect($this->_helper->url->url(array(), 'home'));
    }
    
    /**
     * Display contact form
     */
    public function contactAction()
    {
    }
    
    /**
     * Display rules and regulations
     */
    public function rulesAction()
    {
    }


}

