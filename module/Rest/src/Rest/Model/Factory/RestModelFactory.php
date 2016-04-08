<?php
namespace Rest\Model\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Rest\Model\Rest;

class RestModelFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $sm)
	{
		return new Rest($sm);
	}
}