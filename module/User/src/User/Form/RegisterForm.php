<?php
namespace User\Form;

use Zend\Form\Form;
use Zend\Form\Element\Captcha;
use Zend\Captcha\Image as CaptchaImage;
use Zend\Form\Element\Input;

class RegisterForm extends Form
{

    public function __construct($name = null)
    {
        parent::__construct('register');
        $this->url = $name;
        
        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden'
        ));
        
        $this->add(array(
            'name' => 'email',
            'type' => 'Text',
            'options' => array(
                'label' => 'Email'
            ),
            'attributes' => array(
                'class' => 'light-box-content-textbox',
                'id' => 'txtEmail',
                'placeholder' => 'Enter Email'
            )
        ));
        
        $this->add(array(
            'name' => 'username',
            'type' => 'Text',
            'options' => array(
                'label' => 'User Name'
            ),
            'attributes' => array(
                'class' => 'light-box-content-textbox',
                'id' => 'txtRegUsername',
                'placeholder' => 'Enter Username'
            )
        ));
        
        $this->add(array(
            'name' => 'firstname',
            'type' => 'Text',
            'options' => array(
                'label' => 'First Name'
            ),
            'attributes' => array(
                'class' => 'light-box-content-textbox',
                'id' => 'txtRegFirstname',
                'placeholder' => 'Enter Firstname'
            )
        ));
        
        $this->add(array(
            'name' => 'lastname',
            'type' => 'Text',
            'options' => array(
                'label' => 'Last Name'
            ),
            'attributes' => array(
                'class' => 'light-box-content-textbox',
                'id' => 'txtRegLastname',
                'placeholder' => 'Enter Lastname'
            )
        ));
        
        $this->add(array(
            'name' => 'password',
            'type' => 'password',
            'options' => array(
                'label' => 'Create Password'
            ),
            'attributes' => array(
                'class' => 'light-box-content-textbox',
                'id' => 'txtPass',
                'placeholder' => 'Enter Password'
            )
        ));
        
        $this->add(array(
            'name' => 'confirmpassword',
            'type' => 'password',
            'options' => array(
                'label' => 'Confirm Password'
            ),
            
            'attributes' => array(
                'class' => 'light-box-content-textbox',
                'id' => 'txtconfirmPass',
                'placeholder' => 'Confirm Password'
            )
        ));
        
        $this->captcha = new CaptchaImage(array(
            'expiration' => '300',
            'wordlen' => '6',
            'dotNoiseLevel' => 8,
            'lineNoiseLevel' => 2,
            'font' => getcwd() . '/public/captcha/OpenSans-Regular.ttf',
            'fontSize' => '20',
            'imgDir' => getcwd() . '/public/captcha',
            'imgUrl' => '/captcha'
        ));
        
        $this->add(array(
            'name' => 'captcha',
            'type' => 'Zend\Form\Element\Captcha',
            'options' => array(
                'label' => 'Please verify you are human.',
                'captcha' => $this->captcha
            ),
            'attributes' => array(
                'class' => 'capt',
                'id' => 'captchaimg_signup'
            )
        ));
        
        $this->add(array(
            'name' => 'refreshcaptcha',
            'type' => 'button',
            'attributes' => array(
                'value' => 'Refresh',
                'id' => 'refreshbutton'
            )
        ));
        
        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => array(
                'value' => 'Register'
            ),
            
            'attributes' => array(
                'id' => 'btnRegister',
                'class' => 'btn float-left'
            )
        ));
    }
}