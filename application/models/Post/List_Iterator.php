<?php

class Application_Model_Post_List implements Iterator,Countable
{
    protected $_count;
    protected $_gateway;
    protected $_resultSet;

    public function __construct($results, $gateway)
    {
        $this->setGateway($gateway);
        $this->_resultSet = $results;
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

    public function count()
    {
        if (null === $this->_count) {
            $this->_count = count($this->_resultSet);
        }
        return $this->_count;
    }

    public function current()
    {
        if ($this->_resultSet instanceof Iterator) {
            $key = $this->_resultSet->key();
        } else {
            $key = key($this->_resultSet);
        }
        $result  = $this->_resultSet[$key];
        if (!$result instanceof Application_Model_Post) {
            $gateway = $this->getGateway();
            $result  = $gateway->createPost($result);
            $this->_resultSet[$key] = $result;
        }
        return $result;
    }

    public function key()
    {
        return key($this->_resultSet);
    }

    public function next()
    {
        return next($this->_resultSet);
    }

    public function rewind()
    {
        return reset($this->_resultSet);
    }

    public function valid()
    {
        return (bool) $this->current();
    }
}