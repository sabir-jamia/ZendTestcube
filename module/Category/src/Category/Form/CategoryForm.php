<?php
namespace Category\Form;

use Zend\Form\Form;

class CategoryForm extends Form
{

    public function __construct($name = null)
    {
        parent::__construct('category');
        $this->url = $name;
        $this->setAttribute('class', 'form-horizontal');
        $this->setAttribute('id', 'add-category');
        $this->setAttribute('name', 'add-category');
        $this->setAttribute('role', 'form');
        
        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
            'attributes' => array(
                'id' => 'hide'
            )  
        ));
        
        $this->add(array(
            'name' => 'name',
            'type' => 'Text',
            'options' => array(
                'label' => 'Category Name',
                'label_attributes' => array(
                    'class' => 'col-sm-3 control-label'
                )
            ),
            'attributes' => array(
                'id' => 'name',
                'class' => 'form-control',
                'maxlength' => '30',
                'placeholder' => 'Input your category'
            )
        ));
        
        $this->add(array(
            'name' => 'submit-category',
            'type' => 'Button',
            'options' => array(
                'label' => 'Add',
                'label_options' => array(
                    'disable_html_escape' => true,
                )
            ),
            'attributes' => array(
                'id' => 'submit-category',
                'class' => 'btn btn-primary',
                //'type'  => 'submit'
            )
        ));
    }
}