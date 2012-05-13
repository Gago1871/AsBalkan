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

    public function fetchAwaiting()
    {
        $select = $this->_db_table->select()
            ->where('category IN (0,1)')
            ->where('status = ?', "a")
            ->order('added DESC');
        $posts = $this->_db_table->fetchAll($select);

        return new Application_Model_Post_List($posts, $this);
    }

    public function createPost($data)
    {
        return new Application_Model_Post($data, $this);
    }

    public function getById($id)
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
        switch ($post->category) {
            case 2:
                $result = $this->_db_table->fetchRow('`category` = "' . $post->category . '" AND `moderated` > "' . $post->moderated . '"', 'moderated ASC');
                break;
            
            default:
                $result = $this->_db_table->fetchRow('`category` = "' . $post->category . '" AND `added` > "' . $post->added . '"', 'added ASC');
                break;
        }

        return $result;
    }

    /**
     * Get older post
     */
    private function _getNext(Application_Model_Post $post)
    {
        switch ($post->category) {
            case 2:
                $result = $this->_db_table->fetchRow('`category` = "' . $post->category . '" AND `moderated` < "' . $post->moderated . '"', 'moderated DESC');
                break;
            
            default:
                $result = $this->_db_table->fetchRow('`category` = "' . $post->category . '" AND `added` < "' . $post->added . '"', 'added DESC');
                break;
        }
        
        return $result;
    }
    
    function get($id)
    {
        $posts = new Application_Model_DbTable_Posts();
        $post = new Application_Model_Post($posts->find($id));

        return $post;
    }
}
