<?php
namespace Category\Model\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Category\Model\Category;
use Category\Model\CategoryTable;

class CategoryFactory implements FactoryInterface
{
    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $dbAdapter = $serviceLocator->get('clientdb');
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Category());
        $tableGateway = new TableGateway('category', $dbAdapter, null, $resultSetPrototype);
        $table = new CategoryTable($tableGateway);
        return $table;
    }
}