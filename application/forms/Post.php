<?php

class Application_Form_Post extends Zend_Form
{
    public function init()
    {
        $this->setName('post');
        
        /*
            TODO get url by route name
        */
        $this->setAction('/dodaj');
        


        $www = new Zend_Form_Element_Text('www');
        $www->setLabel('www')
            ->setRequired(false)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty')
            ->setAttrib('placeholder', 'http://www');

        $file = new Zend_Form_Element_File('file');
        $file->setLabel('z dysku')
            ->setRequired(true)
            ->addValidator('Size',
                           false,
                           array('min' => '1kB', 'max' => '100MB'));

        $title = new Zend_Form_Element_Text('title');
        $title->setLabel('Tytuł')
            ->setRequired(false)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty');

        $author = new Zend_Form_Element_Text('author');
        $author->setLabel('Autor')
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty');

        $agreement = new Zend_Form_Element_Checkbox('agreement');
        $agreement->setRequired(true)
            ->setLabel('Akceptuję Regulamin serwisu poebao.pl')
            ->addValidator(new Zend_Validate_InArray(array(1)), false);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submitbutton')
        ->setLabel('Dodaj');
        
        $cancel = new Zend_Form_Element_Text('cancel');
        // $cancel->setXhtml('sfsdfsdf');
        
        $submit->setDecorators(array(
               'ViewHelper',
               array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element',  'placement' => 'prepend', 'openOnly' => 'true')),
               array(array('label' => 'HtmlTag'), array('tag' => 'td', 'placement' => 'prepend')),
           )); 

           $cancel->setDecorators(array(
               'ViewHelper',
               array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element', 'placement' => 'append', 'closeOnly' => 'true')),
           ));
           
           
        $this->addElements(array($www, $file, $title, $author, $agreement, $submit, $cancel));
    }
}