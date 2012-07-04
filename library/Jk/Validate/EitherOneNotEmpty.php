<?php

class Jk_Validate_EitherOneNotEmpty extends Zend_Validate_Abstract
{

    const NOT_PRESENT = 'notPresent';

    protected $_messageTemplates = array(
        self::NOT_PRESENT => 'At least one contact phone shall be provided!'
    );

    protected $_listOfFields;

    public function __construct(array $listOfFields)
    {
        $this->_listOfFields = $listOfFields;
        var_dump($listOfFields);exit;
    }

    public function isValid($value, $context = null)
    {
        var_dump($context);exit;
    }
}