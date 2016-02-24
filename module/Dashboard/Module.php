<?php
namespace Dashboard;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Dashboard\Model\DashUpdates;
use Application\Model\ApplicationTable;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;

use Dashboard\Model\RecentDetails;

class Module
{
	
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig() {
        return array (
            'factories' => array (

                //for test,Result and link table
                'Dashboard\Model\RecentDetails' => function ($sm) {
                    $dbAdapter = $sm->get ('clientdb');
                    $table = new RecentDetails($dbAdapter);
                    return $table;
                },
                'Dashboard\Model\DashUpdates' => function ($sm) {
                	$dbAdapter = $sm->get ( 'clientdb' );
                	$resultSetPrototype = new  DashUpdates($dbAdapter);
                	return $resultSetPrototype;
                },
            )
        );
    }
}

