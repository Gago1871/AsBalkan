<?php

/**
* 
*/
class Application_Model_PostMapper
{

    protected $_db_table;

    protected $_next;
    protected $_previous;

    public function __construct()
    {
        $this->_db_table = new Application_Model_DbTable_Posts();
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
        $post = new Application_Model_Post($result->toArray());

        // fetch previous from category (NEWER)
        $previous = $this->_getPrevious($post);
        if (null !== $previous) {
            $post->setPrevious(new Application_Model_Post($previous->toArray()));
        }
        
        // fetch next form category (OLDER)
        $next = $this->_getNext($post);
        if (null !== $next) {
            $post->setNext(new Application_Model_Post($next->toArray()));
        }

        //return the post object
        return $post;
   }

    private function _getPrevious(Application_Model_Post $post)
    {
        switch ($post->getCategory()) {
            case 2:
                $result = $this->_db_table->fetchRow('`category` = "' . $post->getCategory() . '" AND `moderated` > "' . $post->getModerated() . '"', 'moderated ASC');
                break;
            
            default:
                $result = $this->_db_table->fetchRow('`category` = "' . $post->getCategory() . '" AND `added` > "' . $post->getAdded() . '"', 'added ASC');
                break;
        }

        return $result;
    }

    /**
     * Get older post
     */
    private function _getNext(Application_Model_Post $post)
    {
        switch ($post->getCategory()) {
            case 2:
                $result = $this->_db_table->fetchRow('`category` = "' . $post->getCategory() . '" AND `moderated` < "' . $post->getModerated() . '"', 'moderated DESC');
                break;
            
            default:
                $result = $this->_db_table->fetchRow('`category` = "' . $post->getCategory() . '" AND `added` < "' . $post->getAdded() . '"', 'added DESC');
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
