<?php

class Jk_Validate_Uri extends Zend_Validate_Abstract
{
    const MSG_URI = 'msgUri';

    protected $_messageTemplates = array(
        self::MSG_URI => "Invalid URI",
    );

    public function isValid($value)
    {
        $this->_setValue($value);

        //Validate the URI
        $valid = Zend_Uri::check($value);

        // check if url is valid - alternative solution
        // if (filter_var($file, FILTER_VALIDATE_URL) == false) {
        //     throw new Exception('Seems like it`s not a valid URL...', 1);
        // }
        
        //Return validation result TRUE|FALSE   
        if ($valid)  {
            return true;
        } else {
            $this->_error(self::MSG_URI);
            return false;
        }
    }
}
