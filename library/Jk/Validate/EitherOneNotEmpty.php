<?php

class Jk_Validate_EitherOneNotEmpty extends Zend_Validate_Abstract
{

    const NOT_PRESENT = 'notPresent';

    protected $_messageTemplates = array(
        self::NOT_PRESENT => 'At least one of those fields should not be empty'
    );

    protected $_listOfFields;

    public function __construct(array $listOfFields)
    {
        $this->_listOfFields = $listOfFields;
    }

    public function isValid($value, $context = null)
    {
        Zend_Debug::dump($value);
        Zend_Debug::dump($context);
        // echo $value . ' - ' . print_r($context, true);
        foreach ($this->_listOfFields as $field) {
            if (isset($context[$field]) && !empty($context[$field])) {

                // if (!empty($value)) {
                    // This is the element with content... validate as true
                    return true;
                // }
            
                // we are going to return false and no error
                // to break validation chain on other empty values
                // This is a quick hack, don't have time to invest in this
                return false;
            }
        }

        // All were empty, set your own error message
        $this->_error(self::NOT_PRESENT);
        return false;
    }
}