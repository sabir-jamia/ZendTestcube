<?php
namespace User\Model\Factory;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\ResultSet\ResultSet;
use User\Model\User;
use User\Model\UserTable;
use User\Model\ClientUserTable;
use Zend\Db\TableGateway\TableGateway;

/**
 * Class UserAbstractFactory
 *
 * @author Mohammad Sabir
 */
class UserAbstractFactory implements AbstractFactoryInterface
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
        if($parts[2] == 'ClientUserTable') {
            $requestedName = 'User\Model\UserTable';
        }
        return class_exists($requestedName) && count($parts) == 3 && $parts[0] == 'User' && $parts[1] == 'Model';
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
        $parts = explode("\\", $requestedName);
        if (strtolower(substr($parts[2], - 5)) == 'table') {
            $db = ($parts[2] == 'UserTable') ? 'testcubedb' : 'clientdb';
            $dbAdapter = $serviceLocator->get($db);
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new User());
            $tableGateway = new TableGateway('users', $dbAdapter, null, $resultSetPrototype);
            $table = new UserTable($tableGateway);
            $config = $serviceLocator->get('config');
            $dbConfig = $config['db'];
            $table->setDbCredentails($dbConfig);
            return $table;
        } else {
            return new $requestedName();
        }
    }
}