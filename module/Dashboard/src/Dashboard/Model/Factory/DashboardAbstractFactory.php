<?php
namespace Dashboard\Model\Factory;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\ResultSet\ResultSet;
use Dashboard\Model\DashUpdates;
use Dashboard\Model\RecentDetails;
/**
 * Class DashboardAbstractFactory
 *
 * @author Mohammad Sabir
 */
class DashboardAbstractFactory implements AbstractFactoryInterface
{

    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator            
     * @param $name
     * @param $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $parts = explode("\\", $requestedName);
        return class_exists($requestedName) && count($parts) == 3 && $parts[0] == 'Dashboard' && $parts[1] == 'Model';
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator            
     * @param $name
     * @param $requestedName
     * @return mixed
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return new $requestedName($serviceLocator->get('clientdb'));
    }
}