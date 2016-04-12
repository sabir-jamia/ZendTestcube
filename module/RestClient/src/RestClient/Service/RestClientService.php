<?php
namespace RestClient\Service;

use Zend\ServiceManager\ServiceLocatorInterface;

class RestClientService implements RestClientServiceInterface
{
	protected $sm;
	
	protected $moduleName;
	
	public function __construct($moduleName, ServiceLocatorInterface $sm) 
	{
			$this->moduleName = $moduleName;
			$this->sm = $sm;
	}
	
	public function callRestApi($functionName, $data = array()) 
	{
		$moduleFunc = 'call'.ucfirst($this->moduleName).'Module';
		return $this->sm->get('RestModel')->$moduleFunc($functionName, $data);
	}
	
	public function setModuleName($moduleName)
	{
		$this->moduleName = $moduleName;
		return $this;
	}
	
	public function getModuleName() 
	{
		return $this->moduleName;
	}
}