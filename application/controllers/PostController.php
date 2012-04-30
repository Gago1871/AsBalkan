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

        $this->view->identity = $this->_helper->getIdentity();
    }
    
    public function uploadAction()
    {
        $requestParams = $this->getRequest()->getParams();
        $fromFile = (isset($requestParams['uploadfromfile']) && (1 == $requestParams['uploadfromfile']));

        $form = new Application_Form_Post(array('action' => $this->_helper->url->url(array(), 'postupload'), 'uploadfromfile' => $fromFile));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {

                $appConfig = Zend_Registry::get('Config_App');
                $storage = $appConfig['storage']['location']; // /var/www/poebao-cdn
                $xerocopy = $appConfig['xerocopy'];
                $id = Jk_Url::createUniqueId(); // abcd123
                $hashedDir = Jk_File::getHashedDirStructure($id); // a/b/c/d123
                
                // check where upload is comming from
                $uploadfromfile = $form->getValue('uploadfromfile');

                if ($uploadfromfile) {

                    // upload from file
                    $file = $form->file->getFileName();
                    $form->file->receive();

                    if ($form->file->isReceived()) {
                        $thumbFilename = $this->processFile($file, $id);
                    }
                    $source = 'HD';

                } else {

                    // upload from web
                    $file = $form->getValue('file');
                    $source = 'web';

                    // set_time_limit(0);
                    $tmpfname = tempnam(sys_get_temp_dir(), 'poebao');

                    $fp = fopen($tmpfname, 'w+');//This is the file where we save the information
                    $ch = curl_init($file);//Here is the file we are downloading
                    curl_setopt($ch, CURLOPT_TIMEOUT, 50);
                    curl_setopt($ch, CURLOPT_FILE, $fp);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_exec($ch);
                    curl_close($ch);
                    fclose($fp);

                    $thumbFilename = $this->processFile($file, $id);
                }

                $fileInfo = pathinfo($file);

                $title = $form->getValue('title');
                $author = $form->getValue('author');
                $agreement = $form->getValue('agreement');
                
                // read source of the file
                
                
                $posts = new Application_Model_DbTable_Posts();
                $posts->add($id, $thumbFilename['thumb'], $title, $author, $fileInfo['filename'], $agreement, $source);
                
                // Zend_Controller_Action_Helper_Redirector::goto
                $message = array('type' => 'success', 'content' => 'Twój post został dodany.');
                $this->_helper->getHelper('FlashMessenger')->addMessage($message);

                // $this->_helper->redirector->gotoSimple('view', 'index', null, array('id' => $id, 'title' => $title));
                $this->_helper->redirector->gotoRouteAndExit(array('id' => $id, 'title' => $title), 'view');

            } else {
                
                $message = array('type' => 'failure', 'content' => 'You`re doing it wrong...');
                $this->_helper->getHelper('FlashMessenger')->addMessage($message);
                                $form->populate($formData);
            }
        }

        $this->view->headTitle('Dodaj post');
        $this->view->form = $form;
    }

    public function processFile($file, $id)
    {

        $appConfig = Zend_Registry::get('Config_App');
        $storage = $appConfig['storage']['location']; // /var/www/poebao-cdn
        $xerocopy = $appConfig['xerocopy'];
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

        return $thumbFilename;
    }
}