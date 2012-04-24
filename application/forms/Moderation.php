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

        $defaultDecorator = array(
            // array('Errors', 'placement' => 'prepend'),
            array('ViewHelper'),
            // array('formRadio'),
            // array('Description', array('class' => 'hidden')),
            // array(array('data' => 'HtmlTag'),  array('tag' =>'td', 'class'=> 'element')),
            array('Label', array('tag' => 'span', 'placement' => 'append')),
            array('HtmlTag', array('tag' => 'div', 'class' => 'element'))
        );

        $category = new Zend_Form_Element_Radio('category');
        $category
            ->addMultiOption(0, 'Not moderated')
            ->addMultiOption(1, 'Waiting')
            ->addMultiOption(2, 'Promoted');
        $category->setDecorators($defaultDecorator);

        $nsfw = new Zend_Form_Element_Checkbox('nsfw');
        $nsfw->setLabel('NSFW')
            ->setDecorators($defaultDecorator);

        $removed = new Zend_Form_Element_Checkbox('removed');
        $removed->setLabel('Removed')
            ->setDecorators($defaultDecorator);

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