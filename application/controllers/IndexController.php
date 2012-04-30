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
        $this->view->identity = $this->_helper->getIdentity();
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
        $this->view->posts = $posts->fetchAll('`category` IN ("1", "0") AND `status`="a"', 'added DESC');

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