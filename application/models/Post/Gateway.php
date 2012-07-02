<?php

/**
* 
*/
class Application_Model_Post_Gateway
{

    protected $_db_table;

    protected $_next;
    protected $_previous;

    public function __construct()
    {
        $this->_db_table = new Application_Model_Post_DbTable();
    }

    /**
     * 
     */
    public function createPost($data, $fillDefault = false)
    {
        //set default values if new
        if (true === $fillDefault) {
            $data['added'] = date('Y-m-d H:i:s');
            $data['category'] = Zend_Registry::getInstance()->constants->app->category->unmoderated;
            $data['flag_nsfw'] = 0;
            $data['status'] = 'a';
            $data['author_ip'] = $_SERVER['REMOTE_ADDR'];
        }
        return new Application_Model_Post($data, $this);
    }

    /**
     * 
     */
    public function fetchForMain()
    {
        $select = $this->_db_table->select()
            ->where('category = ?', Zend_Registry::getInstance()->constants->app->category->main)
            ->where('status = ?', "a")
            ->order('moderated DESC')
            ->order('added DESC');
        $posts = $this->_db_table->fetchAll($select);
        consolelog($select);

        return new Application_Model_Post_List($posts, $this);
    }

    /**
     * 
     */
    public function fetchAwaiting()
    {
        $select = $this->_db_table->select()
            ->where('category IN (' . Zend_Registry::getInstance()->constants->app->category->unmoderated . ',' . Zend_Registry::getInstance()->constants->app->category->waiting . ')')
            ->where('status = ?', "a")
            ->order('added DESC');
        $posts = $this->_db_table->fetchAll($select);
        consolelog($select);

        return new Application_Model_Post_List($posts, $this);
    }

    /**
     * 
     */
    public function fetchFromAuthor($author)
    {
        $select = $this->_db_table->select()
            ->where('author = ?', $author)
            ->where('status = ?', "a")
            ->order('added DESC');
        $posts = $this->_db_table->fetchAll($select);
        consolelog($select);

        return new Application_Model_Post_List($posts, $this);
    }

    /**
     * 
     */
    public function fetchPostsAgo($category)
    {
        $select = $this->_db_table->select()
            ->where('status = ?', "a")
            // ->where('id < ?', $agoId)
            ->limit(3, Zend_Registry::getInstance()->constants->app->blocks->postsAgo->offset);

        if (Zend_Registry::getInstance()->constants->app->category->main == $category) {
            $select->order('moderated DESC')
                ->where('category = ?', $category);
        } else {
            $select->order('added DESC')
                ->where('category IN (' . Zend_Registry::getInstance()->constants->app->category->unmoderated . ',' . Zend_Registry::getInstance()->constants->app->category->waiting . ')');
        }

        $posts = $this->_db_table->fetchAll($select);

        consolelog('>' . $select);

        // in case we didnt get enough data
        if ($posts->count() < 3) {
            $select = $this->_db_table->select()
                ->where('status = ?', "a")
                ->limit(3);

            if (Zend_Registry::getInstance()->constants->app->category->main == $category) {
                $select->where('category = ?', $category)
                    ->order('moderated DESC');
            } else {
                $select->where('category IN (' . Zend_Registry::getInstance()->constants->app->category->unmoderated . ',' . Zend_Registry::getInstance()->constants->app->category->waiting . ')')
                    ->order('added DESC');
            }
            consolelog('>' . $select);
            $posts = $this->_db_table->fetchAll($select);
        }

        return new Application_Model_Post_List($posts, $this);
    }

    /**
     * 
     */
    public function fetchForModeration($data)
    {
        $select = $this->_db_table->select()
            ->where('category = ?', $data['category']);
            
        if (isset($data['nsfw']) && 0 == $data['nsfw']) {
            $select->where('flag_nsfw = ?', "0");
        }

        if (isset($data['removed']) && 0 == $data['removed']) {
            $select->where('status = ?', "a");
        }

        if (in_array($data['category'], array(Zend_Registry::getInstance()->constants->app->category->waiting, Zend_Registry::getInstance()->constants->app->category->main))) {
            $select->order('moderated DESC');
        } else {
            $select->order('added ASC');
        }

        $posts = $this->_db_table->fetchAll($select);

        return new Application_Model_Post_List($posts, $this);
    }

    /**
     * 
     */
    public function getByPostId($id, $context = null)
    {
        //use the Table Gateway to find the row that
        //the id represents
        // $result = $this->_db_table->find($id);
        $result = $this->_db_table->fetchRow('`post_id` = "' . $id . '"');

        //if not found, throw an exsception
        if (null === $result) {
            throw new Exception('Post not found');
        }
 
        //if found, get the result, and map it to the
        //corresponding Data Object
        $post = new Application_Model_Post($result->toArray(), $this);

        // fetch previous from category (NEWER)
        $previous = $this->_getPrevious($post, $context);
        if (null !== $previous) {
            $post->setPrevious(new Application_Model_Post($previous->toArray(), $this));
        }
        
        // fetch next form category (OLDER)
        $next = $this->_getNext($post, $context);
        if (null !== $next) {
            $post->setNext(new Application_Model_Post($next->toArray(), $this));
        }

        //return the post object
        return $post;
   }

    /**
     * Get newer post
     */
    private function _getPrevious(Application_Model_Post $post, $context)
    {
        $select = $this->_db_table->select();
        $select->where('status = ?', "a");
        // $select->where('flag_nsfw = ?', "0");

        if (null !== $context) {
            $select->where('author = ?', $post->author);
            $select->where('added > ?', $post->added);
            $select->order('added ASC');
        } else {

            if (Zend_Registry::getInstance()->constants->app->category->main == $post->category) {
                $select->where('category = ?', $post->category);
                $select->where('moderated > ?', $post->moderated);
                $select->order('moderated ASC');
            } else {
                $select->where('category IN (' . Zend_Registry::getInstance()->constants->app->category->unmoderated . ',' . Zend_Registry::getInstance()->constants->app->category->waiting . ')');
                $select->where('added > ?', $post->added);
                $select->order('added ASC');
            }
        }

        return $this->_db_table->fetchRow($select);
    }

    /**
     * Get older post
     */
    private function _getNext(Application_Model_Post $post, $context)
    {
        $select = $this->_db_table->select();
        $select->where('status = ?', "a");
        // $select->where('flag_nsfw = ?', "0");

        if (null !== $context) {
            $select->where('author = ?', $post->author);
            $select->where('added < ?', $post->added);
            $select->order('added DESC');
        } else {

            if (Zend_Registry::getInstance()->constants->app->category->main == $post->category) {
                $select->where('category = ?', $post->category);
                $select->where('moderated < ?', $post->moderated);
                $select->order('moderated DESC');
            } else {
                $select->where('category IN (' . Zend_Registry::getInstance()->constants->app->category->unmoderated . ',' . Zend_Registry::getInstance()->constants->app->category->waiting . ')');
                $select->where('added < ?', $post->added);
                $select->order('added DESC');
            }
        }

        return $this->_db_table->fetchRow($select);
    }

    /**
     * 
     */
    public function getDbTable()
    {
        return $this->_db_table;
    }
}