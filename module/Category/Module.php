<?php
namespace Category;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use Category\Model\Category;
use Category\Model\CategoryTable;

use Question\Model\Question;
use Question\Model\QuestionTable;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;

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

     public function getServiceConfig()
    {
     
        return array(
            'factories' => array(
                'Question\Model\QuestionTable' => function($sm) {
                    $tableGateway = $sm->get('QuestionTableGateway');
                    $table        = new QuestionTable($tableGateway);
                    return $table;
                },
                'QuestionTableGateway' => function($sm) {
                    $dbAdapter          = $sm->get('clientdb');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Question());
                    return new TableGateway('questions', $dbAdapter, null, $resultSetPrototype);
                },
                'Category\Model\CategoryTable' => function($sm) {
                    $tableGateway = $sm->get('CategoryTableGateway');
                    $table        = new CategoryTable($tableGateway);
                    return $table;
                },
                'CategoryTableGateway' => function($sm) {
                    $dbAdapter          = $sm->get('clientdb');                    
                    $resultSetPrototype = new ResultSet();                    
                    $resultSetPrototype->setArrayObjectPrototype(new Category());
                    return new TableGateway('category', $dbAdapter, null, $resultSetPrototype);
                }, 
            ),
        );
        
    }
}
