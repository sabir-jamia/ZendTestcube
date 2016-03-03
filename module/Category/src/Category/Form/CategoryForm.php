<?php
/**
 *@author : Manish Gadhock
 *@date : 30-06-2014
 *@desc : Category Form class
 */

namespace Category\Form;

use Zend\Form\Form;



class CategoryForm extends Form 
{
	public function __construct($name = null)
	{
		parent::__construct('category');
		$this->url = $name;

		$this->add(array(
			'name' => 'id',
			'type' => 'Hidden',
			'attributes' => array(
            	 'id'    => 'hide'
            )

		));

		$this->add(array(
			'name' => 'name',
			'type' => 'Text',
			'options' => array(
			    'label' => 'Category Name',
		    ),
		    'attributes' => array(
            	 'id'    => 'txtCategoryName',
            	 'class' => 'form-control margin-left50',
            	 'maxlength' => '30',
            	 'placeholder' => 'max 30 characters'
            )		
		));

		$this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => array(
                'value' => 'Add',
                'id'    => 'submitbutton'
            )
        ));
	}



}