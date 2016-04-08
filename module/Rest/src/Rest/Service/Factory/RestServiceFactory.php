<?php
namespace Rest\Service\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Rest\Service\RestService;

class RestServiceFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $sm) 
	{
		return new RestService($sm);
	}
}