<?php
namespace RestClient\Model\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use RestClient\Model\Rest;

class RestModelFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $sm)
    {
        //$sm = $sm->getServiceLocator();
        $config = $sm->get('Config');
        $path = $config['TestCubeApi']['uri'];
        return new Rest($sm, $path);
    }
}