<?php

class PostController extends Zend_Controller_Action
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
    
    /**
     * Upload post
     */
    public function uploadAction()
    {
        $fromFile = (isset($this->params['uploadfromfile']) && (1 == $this->params['uploadfromfile']));

        // Create new form
        $form = new Application_Form_Post(array('action' => $this->_helper->url->url(array(), 'postupload'), 'uploadfromfile' => $fromFile));

        if ($this->getRequest()->isPost()) {

            if ($form->isValid($this->params)) {
                
                // check where upload is comming from
                $uploadfromfile = $form->getValue('uploadfromfile');

                if ($uploadfromfile) {

                    // upload from file
                    $file = $form->file->getFileName();
                    $form->file->receive();

                    if (!$form->file->isReceived()) {
                        $message = array('type' => 'failure', 'content' => 'Unable to receive file.');
                        $this->_helper->getHelper('FlashMessenger')->addMessage($message);
                        $form->populate($this->params);
                        $this->view->headTitle('Dodaj post');
                        $this->view->form = $form;
                        return;
                    }
                    $source = 'hd: ' . $file;

                } else {

                    // upload from web
                    $file = $form->getValue('file');
                    $source = 'www: ' . $file;

                    try {
                        $file = Jk_File::download($file);    
                    } catch (Exception $e) {
                        $message = array('type' => 'failure', 'content' => 'Seems like it\'s not a valid URL...');
                        $this->_helper->getHelper('FlashMessenger')->addMessage($message);
                        $form->populate($this->params);
                        $this->view->headTitle('Dodaj post');
                        $this->view->form = $form;
                        return;
                    }
                }

                // check file type based on mime type
                $mime = Jk_File::getMimeType($file);
                switch ($mime) {
                    case 'image/jpeg':
                    case 'image/gif':
                    case 'image/png':
                        break;
                    default:
                        $message = array('type' => 'failure', 'content' => $mime . ' filetype is not supported.');
                        $this->_helper->getHelper('FlashMessenger')->addMessage($message);
                        $form->populate($this->params);
                        $this->view->headTitle('Dodaj post');
                        $this->view->form = $form;
                        return;
                }

                $thumbnailData = $this->xerocopy($file);

                $fileInfo = pathinfo($file);

                $title = $form->getValue('title');
                $author = $form->getValue('author');
                $agreement = $form->getValue('agreement');
                // $source = $file;
                
                // read source of the file
                
                
                $posts = new Application_Model_DbTable_Posts();
                $posts->add($thumbnailData['id'], $thumbnailData['thumb'], $title, $author, $fileInfo['filename'], $agreement, $source);
                
                $message = array('type' => 'success', 'content' => 'Twój post został dodany.');
                $this->_helper->getHelper('FlashMessenger')->addMessage($message);

                $this->_helper->redirector->gotoRouteAndExit(array('id' => $thumbnailData['id'], 'title' => $title), 'postview');

            } else {
                
                $message = array('type' => 'failure', 'content' => 'You`re doing it wrong...');
                $this->_helper->getHelper('FlashMessenger')->addMessage($message);
                $form->populate($this->params);
            }
        }

        $this->view->headTitle('Dodaj post');
        $this->view->form = $form;
    }

    private function xerocopy($file)
    {

        $appConfig = Zend_Registry::get('Config_App');
        $storage = $appConfig['storage']['location']; // /var/www/poebao-cdn
        $xerocopy = $appConfig['xerocopy'];

        $id = Jk_Url::createUniqueId(); // abcd123
        $hashedDir = Jk_File::getHashedDirStructure($id); // a/b/c/d123

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

        $thumbFilename['id'] = $id;
        return $thumbFilename;
    }
}