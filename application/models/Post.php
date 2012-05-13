<?php

/**
* 
*/
class Application_Model_Post
{

    protected $_gateway = null;

    protected $_data = array(
        'id' => null,
        'post_id' => null,
        'title' => null,
        'author' => null,
        'source' => null,
        'added' => null,
        'moderated' => null,
        'category' => null,
        'file' => null,
        'flag_nsfw' => null,
        'status' => null,
        'original_file' => null,
        'author_ip' => null,
        'updated' => null,
        'agreement' => null,
        );

    protected $_next = null;
    protected $_previous = null;
    
    public function __construct($data, $gateway)
    {
        $this->setGateway($gateway);
        $this->populate($data);

        if (!isset($this->post_id)) {
            // throw new Exception('Initial data must contain an id');
        }
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

    public function populate($data)
    {
        if ($data instanceof Zend_Db_Table_Row_Abstract) {
            $data = $data->toArray();
        } elseif (is_object($data)) {
            $data = (array) $data;
        }

        if (!is_array($data)) {
            throw new Exception('Initial data must be an array or object');
        }

        foreach ($data as $key => $value) {
            $this->$key = $value;
        }

        return $this;
    }

    public function __set($name, $value)
    {
        if (!array_key_exists($name, $this->_data)) {
            throw new Exception('Invalid ' . get_class() . ' property "' . $name . '"');
        }
        $this->_data[$name] = $value;
    }

    public function __get($name)
    {
        if (!array_key_exists($name, $this->_data)) {
            throw new Exception('Invalid property "' . $name . '"');
        }

        return $this->_data[$name];
    }

    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }

    public function __unset($name)
    {
        if (isset($this->$name)) {
            $this->_data[$name] = null;
        }
    }

    public function setNext(Application_Model_Post $value)
    {
        $this->_next = $value;
    }

    public function getNext()
    {
        return $this->_next;
    }

    public function setPrevious(Application_Model_Post $value)
    {
        $this->_previous = $value;
    }

    public function getPrevious()
    {
        return $this->_previous;
    }

    /**
     * Insert or update, depending on existence of _data['id']
     */
    public function save()
    {
        $gateway = $this->getGateway();
        if (null === $this->id) {
            $gateway->getDbTable()->insert($this->_data);
        } else {
            // update only data that is set, remove null
            $data = $this->_data;
            unset($data['id']);
            foreach ($data as $key => $value) {
                if (null === $value) {
                    unset($data[$key]);
                }
            }
            $gateway->getDbTable()->update($data, '`id` = "' . $this->_data['id'] . '"');
        }
    }
}