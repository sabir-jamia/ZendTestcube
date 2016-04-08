<?php
 namespace User\Controller\Factory;
 
 use Zend\ServiceManager\FactoryInterface;
 use Zend\ServiceManager\ServiceLocatorInterface;
 use User\Controller\UserController;
 
 class UserControllerFactory implements FactoryInterface
 {
 	public function createService(ServiceLocatorInterface $sm)
 	{
 		$sm = $sm->getServiceLocator();
 		return new UserController($sm);
 	}
 }