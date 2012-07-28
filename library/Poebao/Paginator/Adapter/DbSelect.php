<?php

/**
 * 
 */
class Poebao_Paginator_Adapter_DbSelect extends Zend_Paginator_Adapter_DbSelect
{
    /**
     * Returns a Zend_Db_Table_Rowset_Abstract of items for a page.
     *
     * @param  integer $offset Page offset
     * @param  integer $itemCountPerPage Number of items per page
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $this->_select->limit($itemCountPerPage, $offset);
        $posts = $this->_select->query()->fetchAll();

        $list = array();
        foreach ($posts as $key => $value) {

            $model = new Application_Model_Post($value);
            // $model->setTitle($row->article_title);
            $list[] = $model;
        }
        // return new Application_Model_Post_List($posts);

        return $list;
    }
}