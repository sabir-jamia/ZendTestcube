<?php
namespace Certificate\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Application implements InputFilterAwareInterface
{

    public $id;

    public $title;

    public $assigned_to;

    public $email;

    public $status;

    public $created_on;

    public $created_by;

    public $updated_on;

    public $updated_by;

    public $first_name;

    public $last_name;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (! empty($data['id'])) ? $data['id'] : null;
        $this->title = (! empty($data['title'])) ? $data['title'] : null;
        $this->assigned_to = (! empty($data['assigned_to'])) ? $data['assigned_to'] : null;
        $this->assigned_to = (! empty($data['email'])) ? $data['email'] : null;
        $this->status = (! empty($data['status'])) ? $data['status'] : null;
        $this->created_on = (! empty($data['created_on'])) ? $data['created_on'] : null;
        $this->created_by = (! empty($data['created_by'])) ? $data['created_by'] : null;
        $this->updated_on = (! empty($data['updated_on'])) ? $data['updated_on'] : null;
        $this->updated_by = (! empty($data['updated_by'])) ? $data['updated_by'] : null;
        $this->first_name = (! empty($data['first_name'])) ? $data['first_name'] : null;
        $this->last_name = (! empty($data['last_name'])) ? $data['last_name'] : null;
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
                'name' => 'created_on',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'date'
                    )
                )
            ));
            
            $inputFilter->add(array(
                'name' => 'updated_on',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'date'
                    )
                )
            ));
            
            $inputFilter->add(array(
                'name' => 'status',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'Int'
                    )
                )
            ));
            
            $inputFilter->add(array(
                'name' => 'title',
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
                            'min' => 1,
                            'max' => 100
                        )
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
                                \Zend\Validator\NotEmpty::IS_EMPTY => "Email can't be left blank"
                            )
                        )
                    ),
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => "/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})$/",
                            'messages' => array(
                                \Zend\Validator\Regex::INVALID => 'Invalid Email'
                            )
                        )
                    )
                )
            ));
            
            $inputFilter->add(array(
                'name' => 'assigned_to',
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
                            'min' => 1,
                            'max' => 100
                        )
                    )
                )
            ));
            
            $this->inputFilter = $inputFilter;
        }
        
        return $this->inputFilter;
    }
}