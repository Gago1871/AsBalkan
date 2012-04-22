<?php

class Application_Form_Post extends Zend_Form
{
    
    private $fromFile;

    public function init()
    {
        // $this->startForm();
    }



    public function setmyvar($var)
    {
        $this->fromFile = $var;
    }

    public function startForm()
    {

        // $fromFile = $this->fromFile;

        $this->setName('post');
        
        /*
            TODO get url by route name
        */
        $this->setAction('/dodaj');

        $defaultDecorator = array(
            array('Errors', 'placement' => 'prepend'),
            array('ViewHelper'),
            array('Description', array('class' => 'hidden')),
            array(array('data' => 'HtmlTag'),  array('tag' =>'td', 'class'=> 'element')),
            array('Label', array('tag' => 'td', 'escape' => false)),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        );

        if ($this->fromFile) {
            $file = new Zend_Form_Element_File('file');
            $file
                ->setLabel('Wgraj z dysku <span>(<a href="?file=0">lud z url</a>)</span>')
                ->setRequired(true)
                ->addValidator('Size', false, array('min' => '1kB', 'max' => '20MB'));
        
            $file->class = 'file';
            $file->setDecorators(array(
                array('Errors', 'placement' => 'prepend'),
                array('File'),
                array('Description', array('class' => 'hidden')),
                array(array('data' => 'HtmlTag'),  array('tag' =>'td', 'class'=> 'element')),
                array('Label', array('tag' => 'td', 'escape' => false)),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));
        } else {

            $file = new Zend_Form_Element_Text('www');
            $file->setLabel('www')
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty')
                ->setLabel('url pliku <span>(<a href="?file=1">lud dodaj z dysku</a>)</span>')
                ->setAttrib('placeholder', 'http://www')
                ->setDescription('A to jest opis pola WWW');
            $file->class = 'poebao';

            $file->setDecorators(array(
                array('ViewHelper'),
                array('Errors'),
                // array('Description', array('class' => 'hidden')),
                array(array('data' => 'HtmlTag'),  array('tag' =>'td', 'class'=> 'element', 'colspan' => '2', 'openOnly' => true)),
                array('Label', array('tag' => 'td', 'escape' => false)),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr', 'openOnly' => true))
            ));
        }

        $fromFile = new Zend_Form_Element_Hidden('from_file');

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

        // Source field
        $source = new Zend_Form_Element_Text('source');
        $source->setLabel('Źródło <span>(opcjonalnie)</span>')
            ->setRequired(false)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty')
            ->setDescription('A to jest opis pola Źródło');

        $source->class = 'poebao';
        $source->setDecorators($defaultDecorator);

        // Author field
        $author = new Zend_Form_Element_Text('author');
        $author->setLabel('Autor')
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty')
            ->setDescription('A to jest opis pola autor');

        $author->class = 'poebao';
        $author->setDecorators($defaultDecorator);

        // Agreement checkbox
        $agreement = new Zend_Form_Element_Checkbox('agreement');
        $agreement->setRequired(true)
            ->setLabel('Akceptuję <a href="/regulamin">Regulamin</a> serwisu poebao.pl')
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

        $this->addElements(array($fromFile, $file, $title, $source, $author, $agreement, $submit));

        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form'
        ));
    }
}