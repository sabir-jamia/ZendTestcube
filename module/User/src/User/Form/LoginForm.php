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

      $this->add(array(
      'name' => 'username',
      'type' => 'Text',
      'options' => array(
         //'label' => 'UserName',
         ),
         'attributes' => array(
                     'placeholder' => 'enter your username/email',
                     'id' => 'txtUserName',
                     )	
      ));

      $this->add(array(
      'name' => 'email',
      'id' => 'email',
      'type' => 'hidden',
      ));


      $this->add(array(
      'name' => 'password',
      'type' => 'password',
      'options' => array(
         //'label' => 'Password',
         ),
      'attributes' => array(
                     'placeholder' => 'enter your password',
                     'id' => 'txtPassword'
                     )

      ));

      $this->add(array(
                 'name' => 'login',
                 'type' => 'Submit',
                 'attributes' => array(
                     'value' => 'SignIn',
                     'id'    => 'btnSubmit',
                     'class' => 'button-style'
                 )
             ));

     

      }

  }