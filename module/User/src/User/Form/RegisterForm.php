<?php
namespace User\Form;

use Zend\Form\Form;
use Zend\Form\Element\Captcha;
use Zend\Captcha\Image as CaptchaImage;

class RegisterForm extends Form
{

    public function __construct($name = null)
    {
        parent::__construct('register');
        $this->url = $name;
        $this->setAttribute('class', 'form-horizontal');
        $this->setAttribute('id', 'register');
        $this->setAttribute('name', 'register');
        $this->setAttribute('role', 'form');
        
        $this->add(array(
            'name' => 'userName',
            'type' => 'Text',
            'options' => array(
                'label' => 'User Name',
                'label_attributes' => array(
                    'class' => 'col-md-3 control-label'
                )
            ),
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'username',
                'placeholder' => 'Enter Username'
            )
        ));
        
        $this->add(array(
            'name' => 'firstName',
            'type' => 'Text',
            'options' => array(
                'label' => 'First Name',
                
                'label_attributes' => array(
                    'class' => 'col-md-3 control-label'
                )
            ),
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'firstname',
                'placeholder' => 'Enter Firstname'
            )
        ));
        
        $this->add(array(
            'name' => 'lastName',
            'type' => 'Text',
            'options' => array(
                'label' => 'Last Name',
                'label_attributes' => array(
                    'class' => 'col-md-3 control-label'
                )
            ),
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'lastname',
                'placeholder' => 'Enter Lastname'
            )
        ));
        
        $this->add(array(
            'name' => 'email',
            'type' => 'Text',
            'options' => array(
                'label' => 'Email',
                'label_attributes' => array(
                    'class' => 'col-md-3 control-label'
                )
            ),
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'email',
                'placeholder' => 'Enter Email'
            )
        ));
        
        $this->add(array(
            'name' => 'password',
            'type' => 'password',
            'options' => array(
                'label' => 'Create Password',
                'label_attributes' => array(
                    'class' => 'col-md-3 control-label'
                )
            ),
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'password',
                'placeholder' => 'Enter Password'
            )
        ));
        
        $this->add(array(
            'name' => 'confirmPassword',
            'type' => 'password',
            'options' => array(
                'label' => 'Confirm Password',
                'label_attributes' => array(
                    'class' => 'col-md-3 control-label'
                )
            ),
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'confirm-password',
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
        $this->captcha->setKeepSession(true);
        
        $this->add(array(
            'name' => 'captcha',
            'type' => 'Zend\Form\Element\Captcha',
            'options' => array(
                'label' => 'Please verify you are human.',
                'label_attributes' => array(
                    'class' => 'col-md-3 control-label'
                ),
                'captcha' => $this->captcha
            ),
            'attributes' => array(
                'class' => 'captcha form-control',
                'id' => 'captcha'
            )
        ));
        
        $this->add(array(
            'name' => 'refreshCaptcha',
            'type' => 'button',
            'attributes' => array(
                'value' => 'Refresh',
                'id' => 'refreshbutton'
            )
        ));
        
        $this->add(array(
            'name' => 'registerSubmit',
            'type' => 'submit',
            'attributes' => array(
                'value' => 'Sign Up',
                'id' => 'register-submit',
                'class' => 'btn btn-info'
            )
        ));
    }
}