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
        
        $this->form = new Application_Form_Post();
        $this->view->form = $this->form;
        
        $objRoute = Zend_Controller_Front::getInstance()->getRouter();

        $objRoute =  $objRoute->getRoute('post');

        // var_dump( $objRoute);
    }

    /**
     * Display main site, with approved posts
     */
    public function indexAction() {

        $posts = new Application_Model_DbTable_Posts();
        $this->view->posts = $posts->fetchAll('`status_promoted`="1" AND`status`="a"', 'added DESC');
    }
    
    /**
     * Display content from specified author
     */
    public function authorAction()
    {
        $params = $this->getRequest()->getParams();
        $author = $params['name'];

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
        $this->view->posts = $posts->fetchAll('`status_waiting`="1" AND `status_promoted`="0" AND `status`="a"', 'added DESC');

        $this->view->headTitle('Oczekujące');
        $this->view->title = 'Oczekujące';
    }
    

    /**
     * Display post
     */
    public function viewAction()
    {
        $params = $this->getRequest()->getParams();
        $id = $params['id'];

        $posts = new Application_Model_DbTable_Posts();
        $post = $posts->get($id);
        $this->view->post = $post;

        $this->view->headTitle($post['title']);
        
        $message = array('type' => 'success', 'content' => 'Twój post został dodany.');
        $this->view->message = $message;
    }
    
    public function addAction()
    {
        $form = $this->form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {

                $www = $form->getValue('www');
                
                $appConfig = Zend_Registry::get('Config_App');
                $storage = $appConfig['storage']['location']; // /var/www/poebao-cdn
                $xerocopy = $appConfig['xerocopy'];
                $id = Jk_Url::createUniqueId(); // abcd123
                $hashedDir = Jk_File::getHashedDirStructure($id); // a/b/c/d123
                
                $file = $form->file->getFileName();
                $form->file->receive();
                
                if ($form->file->isReceived()) {
                    
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

                $title = $form->getValue('title');
                $author = $form->getValue('author');
                $agreement = $form->getValue('agreement');
                
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
                                $form->populate($formData);
            }
        }

        $this->view->headTitle('Dodaj post');
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

