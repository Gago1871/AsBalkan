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
        $formData = $this->params;
        $postGateway = new Application_Model_Post_Gateway();

        if (isset($this->params['submit'])) {
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

    public function importerAction()
    {
        $importer = new Poebao_Importer();

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
                    
                    $file = $importer->figureOutExtension($value['filename'], $form->location->getValue());
                    echo '<li>' . $key . ') ' . $file;
                    
                    
                    if (!$file) {
                        echo ' ... is not a file!</li>';
                        continue;
                    }

                    $attachmentId = $this->getInvokeArg('bootstrap')->getResource('xerocopy')->saveImage($file, $file);
                    
                    echo ' - attId: ' . $attachmentId;

                    $fileInfo = pathinfo($file);

                    // Category is always UNMODERATED
                    // $category = $value['category'];
                    $category = Zend_Registry::getInstance()->constants->app->category->unmoderated;
                    $title = $value['title'];
                    $author = $value['author']?$value['author']:$importer->getRandomAuthor();
                    $source = $value['source'];
                    // $source = $value['source']?$value['source']:$importer->getRandomSource();
                    $added = $importer->getRandomDate(date("Y-m-d"), date("Y-m-d"));
                    // $moderated = $importer->getRandomDate($added, '2012-07-31');
                    $moderated = null;
                    $id = Jk_Url::generateUniqueId(); // abcd123

                    $postGateway = new Application_Model_Post_Gateway();
                    $post = $postGateway->createPost(array(
                        'post_id' => $id,
                        'title' => $title,
                        'category' => $category,
                        'author' => $author,
                        'agreement' => true,
                        'source' => $source,
                        'attachment_id' => $attachmentId,
                        'moderated' => $moderated,
                        'added' => $added,
                        'flag_nsfw' => false,
                        'status' => 'a',
                        'author_ip' => $_SERVER['REMOTE_ADDR']
                        ));

                    $result = $post->save();
                    
                    if ($result) {
                        echo ' was saved!';
                    } else {
                        echo ' was not saved (' . mysql_error() . ', ' . $result . ')';
                    }

                    echo '</li>';
                }
            }
        }
    }
}