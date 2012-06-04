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
        $id = $this->_getParam('id');

        $postGateway = new Application_Model_Post_Gateway();
        $post = $postGateway->getByPostId($id);

        $this->view->post = $post;
        $this->view->headTitle($post->title);
        
        $message = array('type' => 'success', 'content' => 'Twój post został dodany.');
        $this->view->message = $message;
    }
    
    /**
     * Upload post
     */
    public function uploadAction()
    {
        // Create new form
        $fromFile = (isset($this->params['uploadfromfile']) && (1 == $this->params['uploadfromfile']));
        $form = new Application_Form_Post(array('action' => $this->_helper->url->url(array(), 'postupload'), 'uploadfromfile' => $fromFile));

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->params)) {
                
                // check where upload is comming from
                $uploadfromfile = $form->getValue('uploadfromfile');

                if ($uploadfromfile) {

                    // upload from file
                    $file = $form->file->getFileName();
                    $form->file->receive();

                    // set source from text field
                    $originalSource = $source = $form->getValue('source');

                    if (!$form->file->isReceived()) {
                        $form->getElement('file')->addError('Unable to download file, please try again later');
                    }
                } else {
                    // upload from web
                    $file = $form->getValue('file');

                    $filedata = parse_url($file);
                    
                    if (!$filedata) {
                        $form->getElement('file')->addError('Unable to download file, please try again later');
                    } else {
                        $source = (!empty($filedata['scheme'])?$filedata['scheme']:'http') . '://' . $filedata['host'];
                        $originalSource = $file;
                    }

                    $file = Jk_File::download($file);
                    if (!$file) {
                        $form->getElement('file')->addError('Unable to download file, please try again later');
                    }
                }

                // we have downloaded file under $file

                // check file type based on mime type
                if (!$form->getElement('file')->hasErrors()) {
                    $mime = Jk_File::getMimeType($file);
                    switch ($mime) {
                        case 'image/jpeg':
                        case 'image/gif':
                        case 'image/png':
                            break;
                        default:
                            $form->getElement('file')->addError($mime . ' filetype is not supported.');
                    }
                }

                // if no errors
                if (!$form->getElement('file')->hasErrors()) {
                    // Do Xerocopy magic - this will create thumbnails
                    $attachmentId = $this->getInvokeArg('bootstrap')->getResource('xerocopy')->saveImage($file, $originalSource);
                    $fileInfo = pathinfo($file);

                    $title = $form->getValue('title');
                    $author = $form->getValue('author');
                    $agreement = $form->getValue('agreement');

                    $id = Jk_Url::generateUniqueId(); // abcd123

                    $postGateway = new Application_Model_Post_Gateway();
                    $post = $postGateway->createPost(array(
                        'post_id' => $id,
                        'title' => $title,
                        'category' => Zend_Registry::getInstance()->constants->app->category->unmoderated,
                        'author' => $author,
                        'agreement' => $agreement,
                        'source' => $source,
                        'attachment_id' => $attachmentId,
                        ), true);

                    $post->save();

                    // Successful upload
                    if ($this->_request->isXmlHttpRequest()) {
                        $result = array('status' => 'success', 'data' => $this->_helper->url->url(array('id' => $id), 'postview'));
                        $this->_helper->json($result);
                    } else {
                        $this->_helper->getHelper('FlashMessenger')->addMessage(array('type' => 'success', 'content' => 'Twój post został dodany.'));
                        $this->_helper->redirector->gotoRouteAndExit(array('id' => $id, 'title' => $title), 'postview');    
                    }
                }
            }

            if ($this->_request->isXmlHttpRequest()) {
                $result = array('status' => 'failure', 'data' => $form->getErrors());
                $this->_helper->json($result);
            }
        }

        $this->view->headTitle('Dodaj post');
        $this->view->form = $form;    
    }
}