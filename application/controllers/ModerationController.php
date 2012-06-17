<?php

class ModerationController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */

        $appConfig = Zend_Registry::get('Config_App');
        $this->view->storageHost = $appConfig['storage']['host'];

        $identity = $this->_helper->getIdentity();

        if (null == $identity) {
            throw new Zend_Controller_Action_Exception('Seem like this page doesn\'t exist ;)', 404);
        }

        $this->view->identity = $identity;

        $this->params = $this->getRequest()->getParams();
    }

    /**
     * Moderation list action
     *
     * @since 2012-04-24
     * @author Jakub Kułak <jakub.kulak@gmail.com>
     */
    public function listAction()
    {
        $this->view->headTitle('Moderacja');
        $this->view->title = 'Moderacja';

        $posts = array();

        $form = new Application_Form_Moderation();
        $formData = $this->requestParams;
        $postGateway = new Application_Model_Post_Gateway();

        if (isset($this->requestParams['submit'])) {
            if ($form->isValid($formData)) {
                $posts = $postGateway->fetchForModeration($formData);
            }
        } else {
            $posts = $postGateway->fetchForModeration(array('category' => Zend_Registry::getInstance()->constants->app->category->unmoderated, 'removed' => 0));
        }

        $paginator = new Jk_Paginator(new Zend_Paginator_Adapter_Array($posts->getList()));
        $paginator->setCurrentPageNumber($this->_getParam('page'));

        $this->view->paginator = $paginator;

        $this->view->posts = $posts;
        $this->view->form = $form;
    }

    /**
     * 
     */
    public function setcategoryAction()
    {
        $id = $this->_getParam('id');
        $category = $this->_getParam('category');

        $postGateway = new Application_Model_Post_Gateway();

        $post = new Application_Model_Post(array(
            'id' => $id,
            'category' => $category,
            'moderated' => date('Y-m-d H:i:s'),
            ), $postGateway);
        $post->save();

        $this->_helper->redirector->gotoRouteAndExit(array(), 'moderation');
    }

    /**
     * 
     */
    public function flagAction()
    {
        $id = $this->_getParam('id');
        $flag = $this->_getParam('flag');
        $value = $this->_getParam('value');

        $postGateway = new Application_Model_Post_Gateway();

        $post = new Application_Model_Post(array(
            'id' => $id,
            'flag_' . $flag => $value,
            ), $postGateway);
        $post->save();

        $this->_helper->redirector->gotoRouteAndExit(array(), 'moderation');
    }

    /**
     * 
     */
    public function statusAction()
    {
        $id = $this->_getParam('id');
        $status = $this->_getParam('status');

        $postGateway = new Application_Model_Post_Gateway();

        $post = new Application_Model_Post(array(
            'id' => $id,
            'status' => $status,
            ), $postGateway);
        $post->save();

        $this->_helper->redirector->gotoRouteAndExit(array(), 'moderation');
    }

    private function _figureOutExtension($filename, $path = null) {
        $extensions = array('jpg', 'jpeg', 'gif', 'png');

        $filebase = $path . $filename;

        foreach ($extensions as $key => $value) {

            $file = $filebase . '.' . $value;
            // echo $file . '<br/>';

            if (file_exists($file)) {
                return $file;
            }
        }

        return false;
    }

    public function importerAction()
    {
        $form = new Application_Form_Importer();
        $this->view->importerForm = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->params)) {

                $file = $form->file->getFileName();
                $form->file->receive();

                $files = array();

                if (($handle = fopen($file, "r")) !== FALSE) {
                    while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
                        $files[] = array(
                            'filename' => $data[0],
                            'category' => ($data[1]=='T')?Zend_Registry::getInstance()->constants->app->category->main:Zend_Registry::getInstance()->constants->app->category->waiting,
                            'title' => $data[2],
                            'author' => $data[3],
                            'source' => $data[4],
                            'params' => $data[5]
                            );
                    }
                }

                // remove header row
                array_shift($files);

                // do the import
                foreach ($files as $key => $value) {
                    $file = $this->_figureOutExtension($value['filename'], $form->location->getValue());
                    
                    if (!$file) {
                        continue;
                    }

                    $attachmentId = $this->getInvokeArg('bootstrap')->getResource('xerocopy')->saveImage($file, $file);
                    $fileInfo = pathinfo($file);

                    $title = $value['title'];
                    $author = $value['author']?$value['author']:'importer';
                    $agreement = true;

                    $source = $value['source']?$value['source']:'http://INTERNET';
                    $category = $value['category'];

                    $id = Jk_Url::generateUniqueId(); // abcd123

                    $postGateway = new Application_Model_Post_Gateway();
                    $post = $postGateway->createPost(array(
                        'post_id' => $id,
                        'title' => $title,
                        'category' => $category,
                        'author' => $author,
                        'agreement' => $agreement,
                        'source' => $source,
                        'attachment_id' => $attachmentId,
                        'moderated' => date('Y-m-d H:i:s'),
                        'added' => date('Y-m-d H:i:s'),
                        'flag_nsfw' => false,
                        'status' => 'a',
                        'author_ip' => $_SERVER['REMOTE_ADDR']
                        ));

                    $post->save();
                }
            }
        }
    }
}