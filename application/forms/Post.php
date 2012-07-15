<?php

class Application_Form_Post extends Zend_Form
{
    public function init()
    {
        $this->setName('formupload');
        $this->setEnctype('multipart/form-data');

        // Create elements
        $url = new Zend_Form_Element_Text('url');
        $file = new Zend_Form_Element_File('file');
        $source = new Zend_Form_Element_Text('source');
        $title = new Zend_Form_Element_Text('title');
        $author = new Zend_Form_Element_Text('author');
        $agreement = new Zend_Form_Element_Checkbox('agreement');
        $fromFile = new Zend_Form_Element_Checkbox('fromFile', array('checked' => 'checked'));
        $submit = new Zend_Form_Element_Submit('submit');
        
        // Set parameters
        $url->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addFilter(new Jk_Filter_Http())
            ->addValidator(new Jk_Validate_Uri())
            ->setLabel('URL pliku')
            ->setAttrib('placeholder', 'http://www');

        $file->setLabel('Wgraj z dysku')
            ->addValidator('MimeType', false, array('image/jpeg', 'image/gif', 'image/png'))
            ->addValidator('Size', false, array('min' => '1kB', 'max' => '4MB'));

        $source->setLabel('Źródło <span>(opcjonalnie)</span>')
            ->addFilter('StripTags')
            ->addFilter('StringTrim');

        $title->setLabel('Tytuł <span>(opcjonalnie)</span>')
            ->addFilter('StripTags')
            ->addFilter('StringTrim');

        $author->setLabel('Autor')
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator(new Zend_Validate_StringLength(array('min' => 3, 'max' => 20)))
            ->addValidator('Alnum');

        $agreement->setDescription('Klikając Dodaj akceptujesz <a href="/regulamin" target="_blank">Regulamin</a> serwisu poebao.pl');

        $submit->setAttrib('id', 'upload-form-submit-button')
            ->setDescription('<a id="closelink" href="/" onclick="hideUploadForm; return false;">Anuluj</a>')
            ->setLabel('Dodaj');

        // Decorate elements
        $file->setDecorators(array(
            array('Errors', array('placement' => 'prepend')),
            'File',
            array(array('li' => 'HtmlTag'), array('tag' => 'li', 'class' => 'element', 'openOnly' => true, 'id' => 'file-sources')),
            ));
        
        $url->setDecorators(array(
            array(array('image' => 'HtmlTag'), array('tag' => 'img', 'src' => 'img/or.png')),            
            'ViewHelper',
            array(array('li' => 'HtmlTag'), array('tag' => 'li', 'class' => 'element', 'closeOnly' => true)),
            ));

        $defaultDecorators = array(
            array('Label', array('tag' => 'span', 'class' => 'desc hidden', 'escape' => false)),
            array('HtmlTag', array('tag' => 'div', 'class' => 'spacer')),
            array('Errors', array('placement' => 'prepend')),
            'ViewHelper',
            array(array('li' => 'HtmlTag'), array('tag' => 'li', 'class' => 'element')),
            );

        $title->setDecorators($defaultDecorators);
        $author->setDecorators($defaultDecorators);
        $source->setDecorators($defaultDecorators);

        $agreement->setDecorators(array(
            array('Label', array('tag' => 'span', 'class' => 'desc hidden', 'escape' => false)),
            array('HtmlTag', array('tag' => 'div', 'class' => 'spacer')),
            array('Errors', array('placement' => 'prepend')),
            // 'ViewHelper',
            array('Description', array('tag' => 'span', 'class' => 'label hidden', 'escape' => false)),
            array(array('li' => 'HtmlTag'), array('tag' => 'li', 'class' => 'element')),
            ));

        $fromFile->setDecorators(array(
            'ViewHelper',
            array('HtmlTag', array('class' => 'hidden'))
            ));

        $submit->setDecorators(array(
            array('HtmlTag', array('tag' => 'span', 'class' => 'desc hidden', 'escape' => false)),
            array('HtmlTag', array('tag' => 'div', 'class' => 'spacer')),
            array('Errors', array('placement' => 'prepend')),
            'ViewHelper',
            array('Description', array('tag' => 'span', 'escape' => false)),
            array(array('li' => 'HtmlTag'), array('tag' => 'li', 'class' => 'element')),
            ));

        // Add elements
        $this->addElements(array($file, $url, $title, $source, $author, $agreement, $fromFile, $submit));

        // Decorate form
        $this->setDecorators(array(
            'FormElements',
            array(array('ul' => 'HtmlTag'), array('tag' => 'ul', 'class' => 'form-list')), // add ul tag around form
            'Form', // add form tag
        ));
    }
}