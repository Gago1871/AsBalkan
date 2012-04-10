<?php

class PostController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function addAction()
    {
        $form = new Application_Form_Post();
        $this->view->form = $form;
        
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
                    $normalizedFilename = Jk_Url::normalize($fileInfo['basename']);
                    
                    $thumbLoc = array();
                    $thumbFilename = array();

                    // start xerocopy magic
                    foreach ($xerocopy['format'] as $key => $format) {
                        if (isset($format['width'])) {
                            $image = Jk_Image::resizeImage($file, $format['width']);
                        } else {
                            $image = self::createImageFromFile($file);
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
                        
                        $thumbFilename[$key] = $thumbLoc[$key] . '-' . $tmpName . '.' . $format['type'];

                        Jk_Image::saveImage($image, $thumbFilename[$key]);
                    }
                }

                $title = $form->getValue('title');
                $author = $form->getValue('author');
                $agreement = $form->getValue('agreement');
                
                $posts = new Application_Model_DbTable_Posts();
                $posts->add($id, $thumbFilename['thumb'], $title, $author, $fileInfo['basename'], $agreement);
                
                // Zend_Controller_Action_Helper_Redirector::goto
                $this->_helper->redirector->gotoRoute(array('id' => $id), 'view');

            } else {
                $form->populate($formData);
            }
        }
    }
}