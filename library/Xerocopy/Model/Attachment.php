<?php

/**
* 
*/
class Xerocopy_Model_Attachment
{
    protected $_gateway = null;

    protected $_data = array(
        'id' => null,
        'filename' => null,
        'added' => null,
        'original_mime' => null,
        'original_size_x' => null,
        'original_size_y' => null,
        'original_filesize' => null,
        'source' => null,
        );
    
    public function __construct($data, $gateway)
    {
        $this->setGateway($gateway);
        $this->populate($data);
    }

    public function setGateway(Xerocopy_Model_Attachment_Gateway $gateway)
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

    /**
     * Insert or update, depending on existence of _data['id']
     */
    public function save()
    {
        $gateway = $this->getGateway();
        if (null === $this->id) {
            $result = $gateway->getDbTable()->insert($this->_data);
        }

        return $result;
    }
}