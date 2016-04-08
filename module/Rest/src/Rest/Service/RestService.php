<?php
namespace Rest\Service;

use Zend\ServiceManager\ServiceLocatorInterface;

class RestService implements RestServiceInterface
{
	protected $sm;
	
	public function __construct(ServiceLocatorInterface $sm) 
	{
			$this->sm = $sm;
	}
	
	public function callRestApi($functionName, $data = array()) 
	{
		$modelFunc = 'call'.ucfirst($data['module']).'Module';
		$this->sm->get('RestModelFactory')->$modelFunc($functionName, $data);
	}
	
}