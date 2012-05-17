<?php

/**
* 
*/
class Xerocopy_Model_Attachment_Gateway
{

    protected $_db_table;

    public function __construct()
    {
        $this->_db_table = new Xerocopy_Model_Attachment_DbTable();
    }

    /**
     * 
     */
    public function create($data, $fillDefault = false)
    {
        return new Xerocopy_Model_Attachment($data, $this);
    }

    /**
     * 
     */
    public function getById($id)
    {
        $result = $this->_db_table->fetchRow('`id` = "' . $id . '"');
        // $result = $this->_db_table->find($id);

        if (null === $result) {
            throw new Exception('Attachment not found');
        }
 
        $post = new Xerocopy_Model_Attachment($result->toArray(), $this);

        return $post;
   }

    /**
     * 
     */
    public function getDbTable()
    {
        return $this->_db_table;
    }
}