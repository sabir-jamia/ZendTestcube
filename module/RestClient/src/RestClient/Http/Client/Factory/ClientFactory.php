<?php
namespace RestClient\Http\Client\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Http\Client as ZendHttpClient;
use RestClient\Http\Client\Client;

class ClientFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $sm)
    {
        $zendClient = new ZendHttpClient();
        return new Client($zendClient, $sm);
    }
}