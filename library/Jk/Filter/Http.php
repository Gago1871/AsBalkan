<?php

/**
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';

class Jk_Filter_Http implements Zend_Filter_Interface
{
    public function filter($value)
    {
        if (substr($value, 0, 7) != 'http://' AND !empty($value))   {
            $value = 'http://' . $value;
        }

        // die($value);

        return $value;
    }
}
