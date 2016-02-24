<?php
namespace User\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\Regex;

class User implements InputFilterAwareInterface
{

    public $username;

    public $firstname;

    public $lastname;

    public $email;

    public $password;

    public $id;

    public $registration_date;

    public $client_id;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (! empty($data['id'])) ? $data['id'] : null;
        $this->username = (! empty($data['username'])) ? $data['username'] : null;
        $this->firstname = (! empty($data['firstname'])) ? $data['firstname'] : null;
        $this->lastname = (! empty($data['lastname'])) ? $data['lastname'] : null;
        $this->email = (! empty($data['email'])) ? $data['email'] : null;
        $this->password = (! empty($data['password'])) ? $data['password'] : null;
        $this->client_id = (isset($data['client_id']) && ! empty($data['client_id'])) ? $data['client_id'] : null;
        $this->registration_date = (isset($data['registration_date']) && ! empty($data['registration_date'])) ? $data['registration_date'] : date("Y-m-d H:i:s");
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();
            
            $inputFilter->add(array(
                'name' => 'id',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'Int'
                    )
                )
            ));
            
            $inputFilter->add(array(
                'name' => 'email',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'StripTags'
                    ),
                    array(
                        'name' => 'StringTrim'
                    )
                ),
                
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8'
                        )
                    ),
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => "Email can't be blank"
                            )
                        )
                    ),
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => "/^([a-zA-Z0-9_\.\-\+0-9])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})$/",
                            'messages' => array(
                                \Zend\Validator\Regex::INVALID => 'Invalid Email'
                            )
                        )
                    )
                )
            ));
            
            // input filter for username
            
            $inputFilter->add(array(
                'name' => 'username',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'StripTags'
                    ),
                    array(
                        'name' => 'StringTrim'
                    )
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 4,
                            'max' => 50,
                            'messages' => array(
                                \Zend\Validator\StringLength::TOO_SHORT => "Username is too short",
                                \Zend\Validator\StringLength::TOO_LONG => "username is too long"
                            )
                        )
                        // \Zend\Validator\NotEmpty::IS_EMPTY => "Username can't be empty"
                        
                        ,
                        'attributes' => array(
                            'class' => 'errorTxt'
                        )
                    ),

                   /*array(
                       'name' => 'Regex',
                       'options' => array(
                           'pattern' => '/^[a-zA-Z0-9_]$/',
                           'messages' => array(
                               \Zend\Validator\Regex::INVALID => "Invalid Username",
                           ),
                       ),
                   ),*/       
                      
                   array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => "Username can't be empty"
                            )
                        )
                    )
                )
            ));
            
            $inputFilter->add(array(
                'name' => 'password',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'StripTags'
                    ),
                    array(
                        'name' => 'StringTrim'
                    )
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 5,
                            'messages' => array(
                                \Zend\Validator\StringLength::TOO_SHORT => "password must be at least 5 characters in length"
                            )
                        )
                    ),
                    
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => "Password can't be empty"
                            )
                        )
                    )
                )
            )
            );
            
            $inputFilter->add(array(
                'name' => 'captcha',
                'required' => false
            ));
            $this->inputFilter = $inputFilter;
            // \Zend\Debug\Debug::dump($this->inputFilter);
        }
        
        return $this->inputFilter;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getUserName()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRegistrationDate()
    {
        return $this->registration_date;
    }

    public function getClientId()
    {
        return $this->client_id;
    }
}