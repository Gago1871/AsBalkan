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
            $data['category'] = 0;
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
            ->where('category = ?', 2)
            ->where('status = ?', "a")
            ->order('moderated DESC')
            ->order('added DESC');
        $posts = $this->_db_table->fetchAll($select);

        return new Application_Model_Post_List($posts, $this);
    }

    /**
     * 
     */
    public function fetchAwaiting()
    {
        $select = $this->_db_table->select()
            ->where('category IN (0,1)')
            ->where('status = ?', "a")
            ->order('added DESC');
        $posts = $this->_db_table->fetchAll($select);

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

        return new Application_Model_Post_List($posts, $this);
    }

    /**
     * 
     */
    public function fetchForModeration($data)
    {
        print_r($data);
        $select = $this->_db_table->select()
            ->where('category = ?', $data['category']);
            
        if (isset($data['nsfw']) && 0 == $data['nsfw']) {
            $select->where('flag_nsfw = ?', "0");
        }

        if (isset($data['removed']) && 0 == $data['removed']) {
            $select->where('status = ?', "a");
        }

        if (in_array($data['category'], array(1, 2))) {
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
    public function getByPostId($id)
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
        $previous = $this->_getPrevious($post);
        if (null !== $previous) {
            $post->setPrevious(new Application_Model_Post($previous->toArray(), $this));
        }
        
        // fetch next form category (OLDER)
        $next = $this->_getNext($post);
        if (null !== $next) {
            $post->setNext(new Application_Model_Post($next->toArray(), $this));
        }

        //return the post object
        return $post;
   }

    /**
     * Get newer post
     */
    private function _getPrevious(Application_Model_Post $post)
    {
        $select = $this->_db_table->select();
        $select->where('status = ?', "a");
        // $select->where('flag_nsfw = ?', "0");

        if (2 == $post->category) {
            $select->where('category = ?', $post->category);
            $select->where('moderated > ?', $post->moderated);
            $select->order('moderated ASC');
        } else {
            $select->where('category IN (0,1)');
            $select->where('added > ?', $post->added);
            $select->order('added ASC');
        }

        return $this->_db_table->fetchRow($select);
    }

    /**
     * Get older post
     */
    private function _getNext(Application_Model_Post $post)
    {
        $select = $this->_db_table->select();
        $select->where('status = ?', "a");
        // $select->where('flag_nsfw = ?', "0");

        if (2 == $post->category) {
            $select->where('category = ?', $post->category);
            $select->where('moderated < ?', $post->moderated);
            $select->order('moderated DESC');
        } else {
            $select->where('category IN (0,1)');
            $select->where('added < ?', $post->added);
            $select->order('added DESC');
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