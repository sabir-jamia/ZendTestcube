<?php
namespace User\Form;

use Zend\Form\Form;

class LoginForm extends Form
{

    public function __construct($name = null)
    {
        parent::__construct('login');
        
        $this->url = $name;
        
        $this->setAttribute('method', 'post');
        $this->setAttribute('role', 'form');
        $this->setAttribute('class', 'form-horizontal');
        
        $this->add(array(
            'name' => 'username',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'username',
                'placeholder' => 'username or email'
            )
        ));      
        
        $this->add(array(
            'name' => 'password',
            'type' => 'password',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'password',
                'placeholder' => 'password'
            )
        ));
        
        $this->add(array(
            'name' => 'email',
            'id' => 'email',
            'type' => 'hidden'
        ));
        
        $this->add(array(
            'name' => 'loginSubmit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Login',
                'id' => 'login-submit',
                'class' => 'btn btn-success'
            )
        ));
    }
}