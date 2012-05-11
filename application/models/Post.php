<?php

/**
* 
*/
class Application_Model_Post
{

    protected $_id;
    protected $_postId;
    protected $_title;
    protected $_author;
    protected $_source;
    protected $_added;
    protected $_moderated;
    protected $_category;

    protected $_file;

    protected $_next = null;
    protected $_previous = null;

    function __construct($data)
    {
        $this
            ->setId($data['id'])
            ->setPostId($data['post_id'])
            ->setTitle($data['title'])
            ->setAuthor($data['author'])
            ->setSource($data['source'])
            ->setAdded($data['added'])
            ->setModerated($data['moderated'])
            ->setCategory($data['category'])
            ->setFile($data['file']);
    }
    
    public function getTitle()
    {
        return $this->_title;
    }

    public function setTitle($title)
    {
        $this->_title = $title;
        return $this;
    }

    public function getAuthor()
    {
        return $this->_author;
    }

    public function setAuthor($author)
    {
        $this->_author = $author;
        return $this;
    }

    public function getSource()
    {
        return $this->_source;
    }

    public function setSource($source)
    {
        $this->_source = $source;
        return $this;
    }

    public function getAdded()
    {
        return $this->_added;
    }

    public function setAdded($added)
    {
        $this->_added = $added;
        return $this;
    }

    public function getFile()
    {
        return $this->_file;
    }

    public function setFile($value)
    {
        $this->_file = $value;
        return $this;
    }

    public function getPostId()
    {
        return $this->_postId;
    }

    public function setPostId($value)
    {
        $this->_postId = $value;
        return $this;
    }

    public function getCategory()
    {
        return $this->_category;
    }

    public function setCategory($value)
    {
        $this->_category = $value;
        return $this;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function setId($value)
    {
        $this->_id = $value;
        return $this;
    }

    public function getModerated()
    {
        return $this->_moderated;
    }

    public function setModerated($value)
    {
        $this->_moderated = $value;
        return $this;
    }

    public function getPrevious()
    {
        return $this->_previous;
    }

    public function setPrevious(Application_Model_Post $value)
    {
        $this->_previous = $value;
        return $this;
    }

    public function getNext()
    {
        return $this->_next;
    }

    public function setNext(Application_Model_Post $value)
    {
        $this->_next = $value;
        return $this;
    }
}
