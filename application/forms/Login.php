<?php

/**
 * Login form
 * @since 2012-04-23
 */
class Application_Form_Login extends Zend_Form
{
 
    /**
     * Init login form
     *
     * @since 2012-04-23
     * @author jakub.kulak@gmail.com
     */
    public function init()
    {
        $login = new Zend_Form_Element_Text('login');
        $login->setLabel('E-mail')
            ->setRequired(true)
            ->addValidator(new Zend_Validate_EmailAddress());

        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('Password')
            ->setRequired(true)
            ->addValidator(new Zend_Validate_StringLength(array('min' => 6, 'max' => 32)));

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Login');

        $this->addElements(array($login, $password, $submit));
    }
}