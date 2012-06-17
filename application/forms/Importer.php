<?php

/**
 * Login form
 * @since 2012-04-23
 */
class Application_Form_Importer extends Zend_Form
{
 
    /**
     * Init importer form
     *
     * @since 2012-06-17
     * @author jakub.kulak@gmail.com
     */
    public function init()
    {
        $this->setName('importer');

        $location = new Zend_Form_Element_Text('location');
        $location->setLabel('Files location')
            ->setRequired(true);

        $file = new Zend_Form_Element_File('file');
        $file->setLabel('File with image data')
            ->setRequired(true);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Importuj');

        $this->addElements(array($location, $file, $submit));
    }
}