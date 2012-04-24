<?php

class Application_Model_DbTable_Posts extends Zend_Db_Table_Abstract
{

    protected $_name = 'posts';

    public function get($id) 
    {
        // $id = (int)$id;
        $row = $this->fetchRow('post_id = "' . $id . '"');
        if (!$row) {
            throw new Exception("Could not find row $id");
        }

        return $row->toArray();    
    }

    public function add($id, $file, $title, $author, $originalFile, $agreement, $source)
    {
        $data = array(
            'post_id' => $id,
            'title' => $title,
            'author' => $author,
            'source' => $source,
            'file' => $file,
            'added' => date('Y-m-d H:i:s'),
            'original_file' => $originalFile,
            'author_ip' => $_SERVER['REMOTE_ADDR'],
            'agreement' => $agreement
        );

        return $this->insert($data);
    }

    public function update($id, $symbol, $name)
    {
        $data = array(
            'symbol' => $symbol,
            'name' => $name,
            );
        $this->update($data, 'post_id = '. (int)$id);
    }

    public function delete($id)
    {
        $this->delete('post_id =' . (int)$id);
    }


    /**
     * Fetch posts from given category
     * 
     * @param integer $category
     * @param boolean $nsfw If NSWF posts should be returned
     * @param boolean $removed If removed posts should be returned
     * @return end_Db_Table_Rowset_Abstract
     * 
     * @since 2012-04-25
     * @author Jakub Kułak <jakub.kulak@gmail.com>
     */
    public function fetchFromCategory($category, $nsfw = true, $removed = false)
    {
        $conditions = '';
        if (!$removed) {
            $conditions .= ' AND `status`="a"';
        }

        if (!$nsfw) {
            $conditions .= ' AND `status_nsfw`="0"';
        }
        return $this->fetchAll('`category`="' . $category . '"' . $conditions, 'added DESC');
    }
}