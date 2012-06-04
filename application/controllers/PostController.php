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
                    $source = $form->getValue('source');
                    $originalSource = $source;

                    if (!$form->file->isReceived()) {
                        // error - file was not received

                        $message = array('type' => 'failure', 'content' => 'Unable to receive file.');
                        // $this->_helper->getHelper('FlashMessenger')->addMessage($message);
                        $this->view->messages[] = $message;
                        $form->populate($this->params);
                        $this->view->headTitle('Dodaj post');
                        $this->view->form = $form;
                        return;
                    }

                } else {

                    // upload from web
                    $file = $form->getValue('file');
                        
                    try {
                        // check if url is valid
                        if (filter_var($file, FILTER_VALIDATE_URL) == false) {
                            throw new Exception('Seems like it`s not a valid URL...', 1);
                        }

                        $filedata = parse_url($file);
                        $source = (!empty($filedata['scheme'])?$filedata['scheme']:'http') . '://' . $filedata['host'];
                        $originalSource = $file;

                        $file = Jk_File::download($file);    
                    } catch (Exception $e) {
                        // error- unable to download file

                        $message = array('type' => 'failure', 'content' => 'Unable to download file, please try again later (' . $e->getMessage() . ')');

                        $form->populate($this->params);
                        if ($this->_request->isXmlHttpRequest()) {
                            $result = array('status'=>'error', 'data' => 'Unable to download file, please try again later (' . $e->getMessage() . ')');
                            $this->_helper->json($result);

                        } else {
                            $this->view->messages[] = $message;
                            $this->view->headTitle('Dodaj post');
                            $this->view->form = $form;
                            return;    
                        }
                        
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
                        $this->view->messages[] = $message;
                        $form->populate($this->params);
                        $this->view->headTitle('Dodaj post');
                        $this->view->form = $form;
                        return;
                }

                // Do Xerocopy magic - this will create thumbnails
                try {
                    $attachmentId = $this->getInvokeArg('bootstrap')->getResource('xerocopy')->saveImage($file, $originalSource);    
                } catch (Exception $e) {
                    // handle any exceptions Xerocopy will throw in future...
                }


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
                
                $message = array('type' => 'success', 'content' => 'Twój post został dodany.');
                $this->_helper->getHelper('FlashMessenger')->addMessage($message);

                if ($this->_request->isXmlHttpRequest()) {
                    $result = array('status'=>'success', 'data' => $this->_helper->url->url(array('id' => $id), 'postview'));
                    $this->_helper->json($result);
                } else {
                    $this->_helper->redirector->gotoRouteAndExit(array('id' => $id, 'title' => $title), 'postview');    
                }
            } else {
                if ($this->_request->isXmlHttpRequest()) {
                    $result = array('status'=>'error', 'data' => $form->getErrors());
                    $this->_helper->json($result);
                } else {
                    $message = array('type' => 'failure', 'content' => 'You`re doing it wrong...');
                    $this->view->messages[] = $message;
                    $form->populate($this->params);
                }
            }
        }

        if ($this->_request->isXmlHttpRequest()) {

        } else {
            $this->view->headTitle('Dodaj post');
            $this->view->form = $form;    
        }
    }
}