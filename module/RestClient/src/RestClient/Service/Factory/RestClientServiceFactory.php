<?php
namespace RestClient\Service\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use RestClient\Service\RestClientService;

class RestClientServiceFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $sm) 
	{
		$routeMatch = $sm->get('Application')->getMvcEvent()->getRouteMatch();
		$controller = $routeMatch->getParam('controller');
		$controllerArr = explode("\\", $controller);
		return new RestClientService($controllerArr[0], $sm);
	}
}