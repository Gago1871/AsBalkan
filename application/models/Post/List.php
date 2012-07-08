<?php

class Application_Model_Post_List
{
    protected $_count;
    protected $_gateway;
    protected $_list = array();

    public function __construct($data = null, $gateway)
    {
        $this->setGateway($gateway);

        if ($data instanceof Zend_Db_Table_Rowset_Abstract) {
            $data = $data->toArray();

            foreach ($data as $key => $value) {
                $this->_list[] = new Application_Model_Post($value, $gateway);
            }
        }
    }

    public function add($item)
    {
        $this->_list[] = $item;
    }

    public function setGateway(Application_Model_Post_Gateway $gateway)
    {
        $this->_gateway = $gateway;
        return $this;
    }

    public function getGateway()
    {
        return $this->_gateway;
    }

    public function getList()
    {
        return $this->_list;
    }
}