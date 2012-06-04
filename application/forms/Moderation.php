<?php

/**
 * Moderation form
 * @since 2012-04-24
 */
class Application_Form_Moderation extends Zend_Form
{
 
    /**
     * Init moderation form
     *
     * @since 2012-04-24
     * @author jakub.kulak@gmail.com
     */
    public function init()
    {
        $this->setName('moderate');
        $this->setMethod('GET');

        $defaultDecorator = array(
            // array('Errors', 'placement' => 'prepend'),
            array('ViewHelper'),
            // array('Description', array('class' => 'hidden')),
            // array(array('data' => 'HtmlTag'),  array('tag' =>'td', 'class'=> 'element')),
            array('Label', array('tag' => 'span', 'placement' => 'append')),
            array('HtmlTag', array('tag' => 'div', 'class' => 'element'))
        );

        $category = new Zend_Form_Element_Radio('category');
        $category
            ->addMultiOption(Zend_Registry::getInstance()->constants->app->category->unmoderated, 'Not moderated')
            ->addMultiOption(Zend_Registry::getInstance()->constants->app->category->waiting, 'Waiting')
            ->addMultiOption(Zend_Registry::getInstance()->constants->app->category->main, 'Promoted')
            ->setValue(0);
        $category->setDecorators($defaultDecorator);
        $category->setSeparator('');

        $nsfw = new Zend_Form_Element_Checkbox('nsfw');
        $nsfw->setLabel('NSFW')
            ->setDecorators($defaultDecorator)
            ->setChecked(true);

        $removed = new Zend_Form_Element_Checkbox('removed');
        $removed->setLabel('Removed')
            ->setDecorators($defaultDecorator)
            ->setChecked(false);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Filter')
            ->setDecorators(array(
                array('ViewHelper'),
                array('HtmlTag', array('tag' => 'span'))));

        $this->addElements(array($category, $nsfw, $removed, $submit));

        $this->setDecorators(array(
            'FormElements',
            // array('HtmlTag', array('tag' => 'div')),
            'Form'
        ));
    }
}