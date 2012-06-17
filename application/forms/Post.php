<?php

class Application_Form_Post extends Zend_Form
{
    public function init()
    {
        // read form attribs passed to constructor in controller
        $attribs = $this->getAttribs();

        $fromFile = (1 == $attribs['uploadfromfile']);

        $this->setName('uploadform');
        $this->setAction($attribs['action']);

        $this->setEnctype('multipart/form-data');

        $defaultDecorator = array(
            array('Errors', 'placement' => 'prepend'),
            array('ViewHelper'),
            array('Description', array('class' => 'hidden')),
            array(array('data' => 'HtmlTag'),  array('tag' => 'td', 'class'=> 'element')),
            array('Label', array('tag' => 'td', 'escape' => false)),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        );

        $uploadFromFile = new Zend_Form_Element_Hidden('uploadfromfile');
        $uploadFromFile->setDecorators(array(
            array('ViewHelper', array('class' => 'hidden'))
        ));

        if ($fromFile) {
            $file = new Zend_Form_Element_File('file');
            $file
                ->setLabel('Wgraj z dysku <span>(<a id="nav-upload-form-switch-source" href="?uploadfromfile=0">lub z url</a>)</span>')
                ->setRequired(true)
                ->addValidator('MimeType', false, array('image/jpeg', 'image/gif', 'image/png'))
                ->addValidator('Size', false, array('min' => '1kB', 'max' => '4MB'));
        
            $file->class = 'file';
            $file->setDecorators(array(
                array('Errors', 'placement' => 'prepend'),
                array('File'),
                array('Description', array('class' => 'hidden')),
                array(array('data' => 'HtmlTag'),  array('tag' =>'td', 'class'=> 'element')),
                array('Label', array('tag' => 'td', 'escape' => false)),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr', 'id' => 'upload-from-file'))
            ));

            $uploadFromFile->setValue(1);

            // Source field
            $source = new Zend_Form_Element_Text('source');
            $source->setLabel('Źródło <span>(opcjonalnie)</span>')
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty')
                ->setDescription('A to jest opis pola Źródło');

            $source->class = 'poebao';
            $source->setDecorators(array(
                array('Errors', 'placement' => 'prepend'),
                array('ViewHelper'),
                array('Description', array('class' => 'hidden')),
                array(array('data' => 'HtmlTag'),  array('tag' => 'td', 'class'=> 'element')),
                array('Label', array('tag' => 'td', 'escape' => false)),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr', 'id' => 'upload-from-file-source'))
            ));

        } else {

            $file = new Zend_Form_Element_Text('file');
            $file->setLabel('www')
                ->setRequired(true)
                
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator(new Jk_Validate_Uri())
                ->addValidator('NotEmpty')
                // ->addValidator('Regex', false, array('/^.*\.(jpg|jpeg|gif|png)$/i'))
                ->setLabel('url pliku <span>(<a id="nav-upload-form-switch-source" href="?uploadfromfile=1">lub dodaj z dysku</a>)</span>')
                ->setAttrib('placeholder', 'http://www')
                ->setDescription('A to jest opis pola WWW');
            $file->class = 'poebao';
            
            $file->setDecorators(array(
                array('Errors', 'placement' => 'prepend'),
                array('ViewHelper'),
                array('Description', array('class' => 'hidden')),
                array(array('data' => 'HtmlTag'),  array('tag' => 'td', 'class'=> 'element')),
                array('Label', array('tag' => 'td', 'escape' => false)),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr', 'id' => 'upload-from-url'))
            ));

            $uploadFromFile->setValue(0);

            $source = new Zend_Form_Element_Hidden('source');
            $source->setDecorators(array(
                array('ViewHelper', array('class' => 'hidden'))
                ));
        }

        // Title field
        $title = new Zend_Form_Element_Text('title');
        $title->setLabel('Tytuł <span>(opcjonalnie)</span>')
            ->setRequired(false)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty')
            ->setDescription('A to jest opis pola tytuł');

        $title->class = 'poebao';
        $title->setDecorators($defaultDecorator);

        // Author field
        $author = new Zend_Form_Element_Text('author');
        $author->setLabel('Autor')
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator(new Zend_Validate_StringLength(array('min' => 3, 'max' => 20)))
            ->addValidator('Alnum')
            ->setDescription('A to jest opis pola autor');

        $author->class = 'poebao';
        $author->setDecorators($defaultDecorator);

        // Agreement checkbox
        $agreement = new Zend_Form_Element_Checkbox('agreement');
        $agreement->setRequired(true)
            ->setLabel('Akceptuję <a href="/regulamin" target="_blank">Regulamin</a> serwisu poebao.pl')
            ->setDescription('Akceptuję Regulamin serwisu poebao.pl')
            ->addValidator(new Zend_Validate_InArray(array(1)), false);
        $agreement->class = 'poebao';
        $agreement->setDecorators(array(
            array('Errors'),
            array('ViewHelper'),
            
            // array('Description', array('class' => 'hidden')),
            array('Label', array('tag' => 'span', 'escape' => false, 'placement' => 'append')),
            array(array('data' => 'HtmlTag'),  array('tag' => 'td')),
            array(array('emptyrow' => 'HtmlTag'),  array('tag' =>'td', 'class'=> 'element',)),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr', 'class' => 'agreement'))
        ));

        // Submit button
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submitbutton')
            ->setDescription('<a id="closelink" href="/" onclick="hideUploadForm; return false;">Anuluj</a>')
            ->setLabel('Dodaj');
        $submit->class = 'submit';
        $submit->setDecorators(array(
            array('ViewHelper'),
            array('Errors'),
            array('Description', array('tag' => 'span', 'escape' => false)),
            array(array('data' => 'HtmlTag'),  array('tag' =>'td', 'class'=> 'element')),
            // array('Label', array('tag' => 'td', 'escape' => false)),
            array(array('emptyrow' => 'HtmlTag'),  array('tag' =>'td', 'class'=> 'element', 'placement' => 'PREPEND')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        $this->addElements(array($uploadFromFile, $file, $title, $source, $author, $agreement, $submit));

        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form'
        ));
    }
}